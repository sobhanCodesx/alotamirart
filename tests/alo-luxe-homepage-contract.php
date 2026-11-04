<?php
declare(strict_types=1);
$root = dirname(__DIR__);
function luxeAssert(bool $ok, string $detail): void {
    if (!$ok) { fwrite(STDERR, "Premium category UI contract failed: " . $detail . "\n"); exit(1); }
}
$home = file_get_contents($root . '/them/app/index.php');
$css = file_get_contents($root . '/public/src/css/alo-home-luxe.css');
$visuals = file_get_contents($root . '/app/Support/home-premium-visuals.php');
luxeAssert(str_contains($home, 'alo-luxe-services'), 'the homepage must include the dedicated premium services section');
luxeAssert(str_contains($home, 'alo-luxe-grid'), 'premium category grid must be rendered');
luxeAssert(str_contains($home, 'alo-luxe-card__picture'), 'large image-led category cards must be rendered');
luxeAssert(str_contains($home, 'alo-luxe-city'), 'premium city CTA must be rendered');
luxeAssert(str_contains($home, 'aloHomeVisualForBrand'), 'category photos must match actual category names');
luxeAssert(str_contains($home, "assets('brands/categories/' . (int)"), 'brand links must preserve their canonical route');
luxeAssert(str_contains($home, "assets('cities')"), 'city CTA must preserve the existing route');
luxeAssert(!str_contains($home, 'alo-explore'), 'previously rejected Explore modal must not be restored');
luxeAssert(str_contains($css, 'object-fit: contain'), 'category images must be fully visible instead of cropped');
luxeAssert(str_contains($css, 'grid-template-columns:repeat(2,minmax(0,1fr))'), 'mobile design must use two large cards per row');
luxeAssert(str_contains($css, '@media (max-width: 1060px)'), 'tablet/mobile breakpoint must prevent tiny four-column cards');
luxeAssert(str_contains($css, 'prefers-reduced-motion'), 'reduced-motion accessibility must be supported');
luxeAssert(str_contains($home, 'alo-home-luxe.css'), 'homepage must load its scoped stylesheet');
luxeAssert(str_contains($visuals, 'public/src/img/home-premium/'), 'category photos must be stored locally');
foreach (['refrigerator','washer','television','support','boiler','air-conditioner','website','expert','advertising','education','science','news','city-skyline','city-technician'] as $name) {
    $file = $root . '/public/src/img/home-premium/' . $name . '.webp';
    luxeAssert(is_file($file), 'missing real WebP image: ' . $name);
    $info = @getimagesize($file);
    luxeAssert(($info['mime'] ?? '') === 'image/webp', 'invalid WebP image: ' . $name);
    luxeAssert(filesize($file) <= 30000, 'image exceeds 30KB: ' . $name);
}
echo "Premium category layout, links and WebP images contract passed\n";
