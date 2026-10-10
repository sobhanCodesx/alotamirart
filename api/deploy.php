<?php
/**
 * AloTamirArt deployment receiver for shared Apache/PHP hosting.
 * Bootstrap manually once, then GitHub Actions can deploy after main merges.
 * PHP 8.1+, ZipArchive. Never deploys config/database.php, .env, or user uploads.
 */
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/Support/env.php';
aloLoadEnv();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function deployReply(int $http, string $status, string $message, array $extra = []): void {
    http_response_code($http);
    echo json_encode(array_merge(['status'=>$status,'message'=>$message], $extra), JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function deployAllowPath(string $path): bool {
    if ($path === '' || strlen($path) > 240 || str_contains($path, '\\') || str_contains($path, "\0") || str_starts_with($path, '/')) return false;
    $parts = explode('/', $path);
    foreach ($parts as $part) if ($part === '' || $part === '.' || $part === '..' || str_starts_with($part, '.')) return false;
    if (!preg_match('~^(api|app|bootstrap|classes|database|public|reqires|router|them|assets|css|js|images|img|fonts)/[a-zA-Z0-9_./-]+$~D', $path)) return false;
    if (preg_match('~(^|/)(uploads?|storage|cache|logs?|vendor|node_modules|backups?|secrets?)(/|$)~i', $path)) return false;
    if (preg_match('~(^|/)(database\.php|\.env|error_log|config\.php)$~i', $path)) return false;
    if (!preg_match('~\.(php|css|js|json|html|htm|txt|svg|png|jpg|jpeg|webp|ico|woff2?)$~i', $path)) return false;
    return true;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') deployReply(405,'error','POST required');
$rootToken = getenv('MCP_API_TOKEN');
if (!is_string($rootToken) || strlen($rootToken) < 32) deployReply(503,'error','Deployment credential is not configured');
$expectedToken = hash_hmac('sha256', 'alotamirart/deployment-auth/v1', $rootToken);
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('~^Bearer ([a-f0-9]{64})$~i', $auth, $m) || !hash_equals($expectedToken, strtolower($m[1]))) deployReply(401,'error','Unauthorized');
if (getenv('ALO_DEPLOY_ENABLED') !== '1') deployReply(503,'error','Deployment disabled');
$sha = strtolower((string) ($_SERVER['HTTP_X_DEPLOY_SHA'] ?? ''));
$sig = strtolower((string) ($_SERVER['HTTP_X_DEPLOY_SIGNATURE'] ?? ''));
if (!preg_match('/^[a-f0-9]{40}$/D', $sha) || !preg_match('/^[a-f0-9]{64}$/D', $sig)) deployReply(400,'error','Invalid request identity');
$length = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($length <= 0 || $length > 20 * 1024 * 1024) deployReply(413,'error','Invalid package size');
$lockDir = sys_get_temp_dir() . '/alotamirart-deploy-' . hash('sha256', __DIR__);
if (!is_dir($lockDir) && !mkdir($lockDir, 0700, true) && !is_dir($lockDir)) deployReply(500,'error','Deployment storage unavailable');
$lock = fopen($lockDir . '/lock', 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) deployReply(409,'error','Deployment already running');
$archivePath = tempnam($lockDir, 'pkg-');
$input = fopen('php://input', 'rb');
$dest = fopen($archivePath, 'wb');
if (!$input || !$dest) deployReply(500,'error','Upload storage unavailable');
$size = stream_copy_to_stream($input, $dest, 20 * 1024 * 1024 + 1);
fclose($input); fclose($dest);
if ($size !== $length || $size > 20 * 1024 * 1024) {unlink($archivePath); deployReply(400,'error','Upload size mismatch');}
$actualHash = hash_file('sha256', $archivePath);
$expectedSig = hash_hmac('sha256', $sha . "\n" . $actualHash, hash_hmac('sha256','alotamirart/deployment-package/v1',$rootToken));
if (!hash_equals($expectedSig, $sig)) {unlink($archivePath); deployReply(401,'error','Invalid package signature');}
$zip = new ZipArchive();
if ($zip->open($archivePath) !== true) {unlink($archivePath); deployReply(422,'error','Invalid ZIP');}
$base = realpath(dirname(__DIR__));
if (!$base || $zip->numFiles < 1 || $zip->numFiles > 400) deployReply(422,'error','Invalid deployment entries');
$entries = []; $total = 0;
for ($i = 0; $i < $zip->numFiles; $i++) {
    $stat = $zip->statIndex($i);
    $path = $stat['name'] ?? '';
    $meta = $zip->getExternalAttributesIndex($i, $opsys, $attributes);
    $unixType = $meta && $opsys === ZipArchive::OPSYS_UNIX ? (($attributes >> 16) & 0170000) : 0100000;
    if (!deployAllowPath($path) || isset($entries[$path]) || $unixType !== 0100000) deployReply(422,'error','Invalid archive path or link');
    $bytes = (int) ($stat['size'] ?? -1);
    if ($bytes < 0 || $bytes > 3 * 1024 * 1024) deployReply(422,'error','Oversized file');
    $total += $bytes;
    if ($total > 35 * 1024 * 1024) deployReply(422,'error','Package expands too large');
    $entries[$path] = $i;
}
$stage = $lockDir . '/stage-' . bin2hex(random_bytes(8));
$backup = $lockDir . '/backup-' . bin2hex(random_bytes(8));
if (!mkdir($stage,0700) || !mkdir($backup,0700)) deployReply(500,'error','Cannot prepare deployment');
$applied = []; $original = [];
try {
    // Preflight all paths before writing any live files.
    foreach ($entries as $path => $index) {
        $target = $base . '/' . $path;
        $segments = explode('/', $path);
        $walk = $base;
        foreach ($segments as $part) {
            $walk .= '/' . $part;
            if (is_link($walk)) throw new RuntimeException('Symlink target rejected');
        }
        if (file_exists($target) && !is_file($target)) throw new RuntimeException('Non-file target rejected');
        $raw = $zip->getFromIndex($index);
        if (!is_string($raw) || strlen($raw) !== (int) $zip->statIndex($index)['size']) throw new RuntimeException('ZIP read failed');
        $stageFile = $stage . '/' . hash('sha256', $path);
        if (file_put_contents($stageFile, $raw, LOCK_EX) !== strlen($raw)) throw new RuntimeException('Staging failed');
    }
    foreach ($entries as $path => $index) {
        $target = $base . '/' . $path;
        $directory = dirname($target);
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) throw new RuntimeException('Directory creation failed');
        if (is_link($directory) || is_link($target)) throw new RuntimeException('Symlink rejected');
        $hadFile = is_file($target);
        if ($hadFile && !copy($target, $backup . '/' . hash('sha256', $path))) throw new RuntimeException('Backup failed');
        $original[$path] = $hadFile;
        if (!rename($stage . '/' . hash('sha256', $path), $target)) throw new RuntimeException('Install failed');
        $applied[] = $path;
    }
    file_put_contents($lockDir . '/last-success.json', json_encode(['sha'=>$sha,'count'=>count($applied),'at'=>gmdate('c')]));
} catch (Throwable $e) {
    for ($i = count($applied)-1; $i >= 0; $i--) {
        $path = $applied[$i]; $destPath = $base . '/' . $path;
        if ($original[$path]) copy($backup . '/' . hash('sha256', $path), $destPath);
        else @unlink($destPath);
    }
    error_log('AloTamirArt deployment failed: ' . $e->getMessage());
    $zip->close(); @unlink($archivePath);
    deployReply(500,'error','Deployment aborted; applied files rolled back where possible');
}
$zip->close(); @unlink($archivePath);
foreach (glob($stage . '/*') ?: [] as $file) @unlink($file);
@rmdir($stage);
foreach (glob($backup . '/*') ?: [] as $file) @unlink($file);
@rmdir($backup);
deployReply(200,'ok','Files installed',['sha'=>$sha,'count'=>count($applied)]);
