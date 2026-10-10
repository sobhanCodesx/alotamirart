<?php
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($get as $entry):
    $slug = trim((string)($entry['slug'] ?? ''), '/');
    if ($slug === '') continue;
    $url = 'https://alotamiratchi.ir/' . rawurlencode($slug) . '/' . (int)$entry['id'];
    $dateValue = $entry['updated_at'] ?? $entry['created_at'] ?? '';
    $lastmod = $dateValue !== '' ? strtotime((string)$dateValue) : false;
?>
  <url>
    <loc><?= htmlspecialchars($url, ENT_XML1, 'UTF-8') ?></loc>
<?php if ($lastmod !== false && $lastmod > 0): ?>
    <lastmod><?= date('Y-m-d', $lastmod) ?></lastmod>
<?php endif; ?>
  </url>
<?php endforeach; ?>
</urlset>
