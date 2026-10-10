<?php
/**
 * Private MCP endpoint for AloTamirArt article management.
 * POST /api/mcp (PlayNexus-style clean endpoint; /api/mcp.php remains compatible).
 * PHP 8.1+; Apache + MySQL; no framework or daemon required.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Support/env.php';
aloLoadEnv(dirname(__DIR__) . '/.env');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function mcpRespond($id, $result = null, $error = null, int $http = 200): void {
    http_response_code($http);
    $body = ['jsonrpc' => '2.0', 'id' => $id];
    if ($error !== null) $body['error'] = $error;
    else $body['result'] = $result;
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function mcpToolResult(array $value): array {
    return ['content' => [['type' => 'text', 'text' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE)]], 'structuredContent' => $value];
}
function mcpFailure(string $message): array {
    return ['isError' => true, 'content' => [['type' => 'text', 'text' => $message]]];
}
function mcpArg(array $a, string $key, int $limit, bool $required = false): string {
    $value = $a[$key] ?? '';
    if (!is_string($value) || ($required && trim($value) === '') || strlen($value) > $limit) {
        throw new InvalidArgumentException('Invalid field: ' . $key);
    }
    return trim($value);
}
function mcpInteger(array $a, string $key, bool $required = false): int {
    $value = $a[$key] ?? null;
    if ($value === null && !$required) return 0;
    if (!is_int($value) && !(is_string($value) && ctype_digit($value))) {
        throw new InvalidArgumentException('Invalid integer: ' . $key);
    }
    $num = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($num === false) throw new InvalidArgumentException('Invalid integer: ' . $key);
    return (int) $num;
}
function mcpDb(): PDO {
    $config = require dirname(__DIR__) . '/config/database.php';
    $db = $config['primary'];
    return new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';charset=utf8mb4',
        $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
}
function mcpPost(PDO $db, int $id): array {
    $st = $db->prepare('SELECT id,title,slug,description,keyword,tags,content,post_id,user_id,status,created_at FROM posts WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) throw new InvalidArgumentException('Article not found.');
    return $row;
}
function mcpMenuExists(PDO $db, int $id): bool {
    $st = $db->prepare('SELECT id FROM menu WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    return (bool) $st->fetchColumn();
}
require_once __DIR__ . '/content-tools.php';
require_once __DIR__ . '/site-graph.php';

function mcpTools(): array {
    $fields = [
        'title' => ['type' => 'string', 'maxLength' => 200],
        'content' => ['type' => 'string', 'maxLength' => 120000],
        'description' => ['type' => 'string', 'maxLength' => 1000],
        'keyword' => ['type' => 'string', 'maxLength' => 250],
        'tags' => ['type' => 'string', 'maxLength' => 1000],
        'post_id' => ['type' => 'integer', 'minimum' => 1, 'description' => 'Existing menu/category id'],
    ];
    return array_merge([
        ['name' => 'list_article_categories', 'description' => 'List categories from the existing menu table.', 'inputSchema' => ['type' => 'object', 'properties' => new stdClass()]],
        ['name' => 'list_articles', 'description' => 'List latest articles, including drafts.', 'inputSchema' => ['type' => 'object', 'properties' => ['limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50]]]],
        ['name' => 'get_article', 'description' => 'Read one article.', 'inputSchema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'minimum' => 1]], 'required' => ['id']]],
        ['name' => 'create_article_draft', 'description' => 'Create an unpublished draft; never publishes immediately.', 'inputSchema' => ['type' => 'object', 'properties' => $fields, 'required' => ['title', 'content', 'post_id']]],
        ['name' => 'update_article_draft', 'description' => 'Edit an existing UNPUBLISHED draft only.', 'inputSchema' => ['type' => 'object', 'properties' => array_merge(['id' => ['type' => 'integer', 'minimum' => 1]], $fields), 'required' => ['id']]],
        ['name' => 'publish_article', 'description' => 'Explicitly publish a draft ONLY after user approval; requires MCP_ALLOW_PUBLISH=1.', 'inputSchema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'minimum' => 1], 'confirm' => ['type' => 'boolean', 'const' => true]], 'required' => ['id', 'confirm']]],
    ], aloContentTools(), aloGraphTools());
}
function mcpExecute(string $name, array $a): array {
    if (in_array($name, ['describe_alo_graph','query_alo_graph'], true)) return aloGraphExecute($name,$a);
    if (in_array($name, ['describe_content_fields','list_content','find_content','get_content','create_content','update_content','set_content_published'], true)) return aloContentExecute($name, $a);
    if ($name === 'publish_article' && getenv('MCP_ALLOW_PUBLISH') !== '1') {
        throw new InvalidArgumentException('Publishing disabled by MCP_ALLOW_PUBLISH.');
    }
    $db = mcpDb();
    if ($name === 'list_article_categories') {
        return ['categories' => $db->query('SELECT id,title FROM menu ORDER BY id DESC LIMIT 100')->fetchAll()];
    }
    if ($name === 'list_articles') {
        $limit = $a['limit'] ?? 20;
        if (!is_int($limit) || $limit < 1 || $limit > 50) throw new InvalidArgumentException('Invalid limit.');
        return ['articles' => $db->query('SELECT id,title,slug,post_id,status,created_at FROM posts ORDER BY id DESC LIMIT ' . $limit)->fetchAll()];
    }
    if ($name === 'get_article') return ['article' => mcpPost($db, mcpInteger($a, 'id', true))];
    if ($name === 'create_article_draft') {
        $title = mcpArg($a, 'title', 200, true);
        $content = mcpArg($a, 'content', 120000, true);
        $cat = mcpInteger($a, 'post_id', true);
        if (!mcpMenuExists($db, $cat)) throw new InvalidArgumentException('Category does not exist.');
        $author = filter_var(getenv('MCP_AUTHOR_USER_ID'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($author === false || $author === null) throw new RuntimeException('MCP_AUTHOR_USER_ID must identify an existing author.');
        $check = $db->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
        $check->execute([$author]);
        if (!$check->fetchColumn()) throw new RuntimeException('Configured author does not exist.');
        $slug = preg_replace('/[^\p{L}\p{N}-]+/u', '', preg_replace('/\s+/u', '-', $title));
        $slug = trim(mb_strtolower($slug, 'UTF-8'), '-');
        $stmt = $db->prepare('INSERT INTO posts (title,slug,content,description,keyword,tags,post_id,user_id,status,created_at) VALUES (?,?,?,?,?,?,?,?,0,NOW())');
        $stmt->execute([$title, $slug, $content, mcpArg($a, 'description', 1000), mcpArg($a, 'keyword', 250), mcpArg($a, 'tags', 1000), $cat, $author]);
        return ['article' => mcpPost($db, (int) $db->lastInsertId()), 'published' => false];
    }
    if ($name === 'update_article_draft') {
        $id = mcpInteger($a, 'id', true);
        $current = mcpPost($db, $id);
        if ((int) $current['status'] !== 0) throw new InvalidArgumentException('Published articles cannot be edited by this draft tool.');
        $allowed = ['title' => 200, 'content' => 120000, 'description' => 1000, 'keyword' => 250, 'tags' => 1000];
        $changes = [];
        foreach ($allowed as $key => $limit) if (array_key_exists($key, $a)) $changes[$key] = mcpArg($a, $key, $limit, in_array($key, ['title', 'content'], true));
        if (array_key_exists('post_id', $a)) {
            $cat = mcpInteger($a, 'post_id', true);
            if (!mcpMenuExists($db, $cat)) throw new InvalidArgumentException('Category does not exist.');
            $changes['post_id'] = $cat;
        }
        if (isset($changes['title'])) {
            $slug = preg_replace('/[^\p{L}\p{N}-]+/u', '', preg_replace('/\s+/u', '-', $changes['title']));
            $changes['slug'] = trim(mb_strtolower($slug, 'UTF-8'), '-');
        }
        if (!$changes) throw new InvalidArgumentException('No fields to update.');
        $sql = 'UPDATE posts SET ' . implode(', ', array_map(static function ($key) {return $key . ' = ?';}, array_keys($changes))) . ', updated_at = NOW() WHERE id = ? AND status = 0';
        $st = $db->prepare($sql);
        $st->execute(array_merge(array_values($changes), [$id]));
        return ['article' => mcpPost($db, $id)];
    }
    if ($name === 'publish_article') {
        if (($a['confirm'] ?? null) !== true) throw new InvalidArgumentException('Explicit confirm=true is required.');
        $id = mcpInteger($a, 'id', true);
        $post = mcpPost($db, $id);
        if (trim((string) $post['title']) === '' || trim((string) $post['content']) === '' || !mcpMenuExists($db, (int) $post['post_id'])) {
            throw new InvalidArgumentException('Article is not ready to publish.');
        }
        $st = $db->prepare('UPDATE posts SET status = 1, updated_at = NOW() WHERE id = ? AND status = 0');
        $st->execute([$id]);
        return ['article' => mcpPost($db, $id), 'published' => true];
    }
    throw new InvalidArgumentException('Unknown tool.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    mcpRespond(null, null, ['code' => -32600, 'message' => 'POST required.'], 405);
}
$secret = getenv('MCP_API_TOKEN');
$authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if (!is_string($secret) || strlen($secret) < 32 || !preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches) || !hash_equals($secret, $matches[1])) {
    mcpRespond(null, null, ['code' => -32001, 'message' => 'Unauthorized.'], 401);
}
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== 0) mcpRespond(null, null, ['code' => -32600, 'message' => 'JSON content type required.'], 415);
if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 4000000) mcpRespond(null, null, ['code' => -32600, 'message' => 'Request too large.'], 413);
$raw = file_get_contents('php://input', false, null, 0, 4000001);
if (strlen($raw) > 4000000) mcpRespond(null, null, ['code' => -32600, 'message' => 'Request too large.'], 413);
$p = json_decode($raw, true);
if (!is_array($p) || array_is_list($p) || ($p['jsonrpc'] ?? '') !== '2.0' || !is_string($p['method'] ?? null)) {
    mcpRespond(null, null, ['code' => -32600, 'message' => 'Invalid JSON-RPC.'], 400);
}
$id = $p['id'] ?? null;
$method = $p['method'];
if ($method === 'notifications/initialized') {http_response_code(202); exit;}
if ($method === 'ping') mcpRespond($id, new stdClass());
if ($method === 'initialize') mcpRespond($id, ['protocolVersion' => '2025-11-25', 'capabilities' => ['tools' => new stdClass()], 'serverInfo' => ['name' => 'alotamirart-articles', 'version' => '1.0.0']]);
if ($method === 'server/discover') mcpRespond($id, ['supportedVersions' => ['2026-07-28'], 'capabilities' => ['tools' => new stdClass()], 'ttlMs' => 3600000, 'cacheScope' => 'private']);
if ($method === 'tools/list') mcpRespond($id, ['tools' => mcpTools()]);
if ($method === 'tools/call') {
    $params = $p['params'] ?? [];
    if (!is_array($params) || !is_string($params['name'] ?? null) || !is_array($params['arguments'] ?? [])) mcpRespond($id, null, ['code' => -32602, 'message' => 'Invalid tool params.']);
    try {mcpRespond($id, mcpToolResult(mcpExecute($params['name'], $params['arguments'] ?? [])));}
    catch (InvalidArgumentException $e) {mcpRespond($id, mcpFailure($e->getMessage()));}
    catch (Throwable $e) {error_log('MCP article action failed: ' . get_class($e)); mcpRespond($id, mcpFailure('Action failed; check server logs.'));}
}
mcpRespond($id, null, ['code' => -32601, 'message' => 'Method not found.']);
