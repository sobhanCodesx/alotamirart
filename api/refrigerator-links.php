<?php
/** Narrow, authenticated MCP operation to migrate links in refrigerator articles. */
declare(strict_types=1);

function aloRefrigeratorLinkTool(): array {
    return [
        'name' => 'replace_refrigerator_o3am_links',
        'description' => 'Replace genuine o3am.com URL hosts with o3am.ir ONLY in regular articles from refrigerator category id 9, including drafts; verify transactionally.',
        'inputSchema' => [
            'type' => 'object',
            'properties' => [
                'category_id' => ['type' => 'integer', 'const' => 9],
                'from_host' => ['type' => 'string', 'const' => 'o3am.com'],
                'to_host' => ['type' => 'string', 'const' => 'o3am.ir'],
                'confirm' => ['type' => 'boolean', 'const' => true],
            ],
            'required' => ['category_id', 'from_host', 'to_host', 'confirm'],
        ],
    ];
}

function aloRefrigeratorLinkValidate(array $args): void {
    if (($args['category_id'] ?? null) !== 9
        || ($args['from_host'] ?? null) !== 'o3am.com'
        || ($args['to_host'] ?? null) !== 'o3am.ir'
        || ($args['confirm'] ?? null) !== true) {
        throw new InvalidArgumentException('Only explicitly confirmed refrigerator-category o3am.com to o3am.ir replacement is permitted.');
    }
}

/** Match entire URL hosts, not email addresses, lookalike domains or path segments. */
function aloRefrigeratorReplaceHost(string $value, int &$replacements): string {
    $pattern = <<<'REGEX'
~(?<![A-Za-z0-9_@./-])((?:https?:)?//)?((?:[A-Za-z0-9-]+\.)*)o3am\.com(?=$|[/:?#\s"'<>),;\]]|&(?:amp;)?)~iu
REGEX;
    $updated = preg_replace_callback($pattern, static function (array $match) use (&$replacements): string {
        $replacements++;
        return ($match[1] ?? '') . ($match[2] ?? '') . 'o3am.ir';
    }, $value);
    if ($updated === null) throw new RuntimeException('Invalid article encoding during URL replacement.');
    return $updated;
}

function aloRefrigeratorLinkExecute(array $args, ?PDO $db = null): array {
    aloRefrigeratorLinkValidate($args);
    $db ??= mcpDb();
    $fields = ['title', 'content', 'description', 'tags'];
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $db->beginTransaction();
    try {
        $sql = 'SELECT id,' . implode(',', array_map(static fn($key) => '`' . $key . '`', $fields)) .
            ' FROM posts WHERE post_id = ? ORDER BY id ASC' . ($driver === 'mysql' ? ' FOR UPDATE' : '');
        $query = $db->prepare($sql);
        $query->execute([9]);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) throw new RuntimeException('No refrigerator posts were found; refusing empty bulk operation.');
        $modified = [];
        $occurrences = 0;
        $fieldCounts = array_fill_keys($fields, 0);
        foreach ($rows as $row) {
            $sets = [];
            $values = [];
            foreach ($fields as $field) {
                $original = (string)($row[$field] ?? '');
                $count = 0;
                $replacement = aloRefrigeratorReplaceHost($original, $count);
                if ($count === 0) continue;
                $sets[] = '`' . $field . '`=?';
                $values[] = $replacement;
                $fieldCounts[$field] += $count;
                $occurrences += $count;
            }
            if (!$sets) continue;
            $values[] = (int)$row['id'];
            $values[] = 9;
            $stmt = $db->prepare('UPDATE posts SET ' . implode(',', $sets) . ' WHERE id=? AND post_id=?');
            $stmt->execute($values);
            if ($stmt->rowCount() !== 1) throw new RuntimeException('Article changed during link replacement; transaction rolled back.');
            $modified[] = (int)$row['id'];
        }
        // Check every field in every record, including drafts, before committing.
        $verify = $db->prepare('SELECT id,' . implode(',', array_map(static fn($key) => '`' . $key . '`', $fields)) .
            ' FROM posts WHERE post_id=?');
        $verify->execute([9]);
        $verifiedRows = $verify->fetchAll(PDO::FETCH_ASSOC);
        if (count($verifiedRows) !== count($rows)) throw new RuntimeException('Article count changed while verifying.');
        foreach ($verifiedRows as $row) {
            foreach ($fields as $field) {
                $left = 0;
                aloRefrigeratorReplaceHost((string)($row[$field] ?? ''), $left);
                if ($left !== 0) throw new RuntimeException('Old URL hosts remain in refrigerator category; transaction rolled back.');
            }
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
    return [
        'category_id' => 9,
        'source' => 'o3am.com',
        'destination' => 'o3am.ir',
        'articles_checked' => count($rows),
        'articles_updated' => count($modified),
        'links_replaced' => $occurrences,
        'changed_by_field' => $fieldCounts,
        'article_ids_updated' => $modified,
        'verified' => true,
    ];
}
