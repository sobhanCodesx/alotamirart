<?php
/**
 * Short anonymous-only homepage microcache.
 *
 * It runs BEFORE sessions and database bootstrap, so cache hits do not connect
 * to MySQL. It never serves pages to requests carrying cookies or credentials.
 * Cache files live in PHP's private system temp directory, not in public_html.
 */
declare(strict_types=1);

function aloHomeCacheEligible(array $server): bool
{
    return ($server['REQUEST_METHOD'] ?? '') === 'GET'
        && ($server['HTTP_HOST'] ?? '') === 'alotamiratchi.ir'
        && ($server['REQUEST_URI'] ?? '') === '/'
        && ($server['QUERY_STRING'] ?? '') === ''
        && ($server['HTTP_COOKIE'] ?? '') === ''
        && ($server['HTTP_AUTHORIZATION'] ?? '') === ''
        && ($server['REDIRECT_HTTP_AUTHORIZATION'] ?? '') === ''
        && ($server['PHP_AUTH_USER'] ?? '') === ''
        && ($server['HTTP_RANGE'] ?? '') === ''
        && stripos((string)($server['HTTP_CACHE_CONTROL'] ?? ''), 'no-cache') === false
        && stripos((string)($server['HTTP_CACHE_CONTROL'] ?? ''), 'no-store') === false;
}

function aloHomeCacheDirectory(string $root, string $temp): string
{
    // No user-controlled path, one private directory per installation.
    return rtrim($temp, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
        . 'alo-home-' . substr(hash('sha256', $root), 0, 20);
}

function aloHomeCacheFile(string $root, string $temp): string
{
    // Rotates automatically when the deployed homepage/layout/bootstrap changes.
    $paths = [
        $root . '/index.php',
        $root . '/them/app/index.php',
        $root . '/them/app/layout/header.php',
        $root . '/them/app/layout/footer.php',
        $root . '/public/src/css/site.css',
    ];
    $fingerprint = 'alo-public-home-v1';
    foreach ($paths as $path) {
        $fingerprint .= '|' . (string)(@filemtime($path) ?: 0) . ':' . (string)(@filesize($path) ?: 0);
    }
    return aloHomeCacheDirectory($root, $temp) . '/home-' . substr(hash('sha256', $fingerprint), 0, 32) . '.html';
}

function aloHomeCacheHtmlIsSafe(string $body): bool
{
    return strlen($body) >= 500 && strlen($body) <= 2 * 1024 * 1024
        && stripos($body, '<!doctype html>') !== false
        && stripos($body, '</html>') !== false;
}

function aloHomeCacheStore(string $file, string $html): void
{
    if (!aloHomeCacheHtmlIsSafe($html)) return;
    try {
        $tmp = @tempnam(dirname($file), 'alo-write-');
        if (!is_string($tmp)) return;
        @chmod($tmp, 0600);
        $size = @file_put_contents($tmp, $html, LOCK_EX);
        if ($size !== strlen($html) || !@rename($tmp, $file)) {
            @unlink($tmp);
        }
    } catch (Throwable $ignored) {
        // Caching is an optional optimization; never interrupt page serving.
    }
}

function aloHomeCacheStart(array $server, string $root, ?string $temp = null): bool
{
    if (!aloHomeCacheEligible($server)) return false;
    $temp ??= sys_get_temp_dir();
    $directory = aloHomeCacheDirectory($root, $temp);
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) return false;

    $file = aloHomeCacheFile($root, $temp);
    $ttl = 75; // Short-lived to reflect newly published content quickly.
    $age = is_file($file) ? time() - (int)@filemtime($file) : PHP_INT_MAX;
    if ($age >= 0 && $age < $ttl) {
        $html = @file_get_contents($file);
        if (is_string($html) && aloHomeCacheHtmlIsSafe($html)) {
            header('Content-Type: text/html; charset=utf-8');
            header('Cache-Control: private, no-store');
            header('X-Alo-Page-Cache: HIT');
            echo $html;
            return true;
        }
    }

    header('X-Alo-Page-Cache: MISS');
    header('Cache-Control: private, no-store');
    ob_start(static function (string $html) use ($file): string {
        if (http_response_code() === 200) aloHomeCacheStore($file, $html);
        return $html;
    });
    return false;
}
