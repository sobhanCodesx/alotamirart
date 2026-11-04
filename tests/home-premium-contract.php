<?php
/** Homepage premium visual contract: exact category destinations and local images. */
declare(strict_types=1);
$root = dirname(__DIR__);
require_once $root . '/app/Support/home-premium-visuals.php';
function premiumAssert(bool $condition, string $message): void {
    if (!$condition) { fwrite(STDERR, "Premium home contract: $message\n"); exit(1); }
}
$samples = [
  'تعمیرات یخچال' => 'refrigerator',
  'تعمیرات ماشین لباسشویی' => 'washer',
  'تعمیرات تلویزیون' => 'television',
  'پشتیبانی الو تعمیراتچی' => 'support',
  'تعمیرات پکیج' => 'boiler',
  'کولر گازی' => 'air-conditioner',
  'o3am' => 'website',
  'متخصصین برتر' => 'expert',
  'خبری' => 'news',
  'مجلات علمی' => 'science',
  'آموزشی و یادگیری' => 'education',
  'تبلیغات ویژه' => 'advertising',
];
foreach ($samples as $title => $image) {
    $result = aloHomeVisualForBrand($title);
    premiumAssert(str_ends_with($result['image'], '/'.$image.'.webp'), "wrong image for $title");
    premiumAssert(trim($result['description']) !== '', "missing description for $title");
    $file = $root.'/'.$result['image'];
    premiumAssert(is_file($file), "missing local asset $image");
    premiumAssert(filesize($file) <= 30000, "asset $image exceeds 30KB");
    $binary = file_get_contents($file);
    premiumAssert(substr($binary, 0, 4) === 'RIFF' && substr($binary, 8, 4) === 'WEBP', "invalid WebP asset $image");
}
$fallback = aloHomeVisualForBrand('دسته‌بندی جدید');
premiumAssert(str_ends_with($fallback['image'], '/website.webp'), 'unknown item must have a valid fallback image');
$home = file_get_contents($root.'/them/app/index.php');
$head = file_get_contents($root.'/them/app/layout/header.php');
$css = file_get_contents($root.'/public/src/css/site.css');
premiumAssert(str_contains($home, 'class="alo-home-premium"'), 'homepage class missing');
premiumAssert(str_contains($home, 'aloHomeVisualForBrand($brandTitle)'), 'category visuals must be topic-aware');
premiumAssert(str_contains($home, "brands/categories/"), 'category URLs must remain unchanged');
premiumAssert(str_contains($home, "assets('cities')"), 'city search destination missing');
premiumAssert(str_contains($home, 'alo-brand-grid'), 'accessible category grid missing');
premiumAssert(str_contains($home, 'alo-latest-brand-section'), 'recent brands layout missing');
premiumAssert(str_contains($head, 'alo-site-wordmark'), 'mobile wordmark missing');
premiumAssert(str_contains($css, '.alo-home-premium .alo-brand-grid'), 'responsive category-grid CSS missing');
premiumAssert(str_contains($css, '@media (max-width: 640px)'), 'mobile breakpoint missing');
premiumAssert(is_file($root.'/public/src/img/home-premium/city-technician.webp'), 'CTA technician photo missing');
premiumAssert(is_file($root.'/public/src/img/home-premium/city-skyline.webp'), 'CTA city photo missing');
echo "Premium homepage visual contracts passed\n";
