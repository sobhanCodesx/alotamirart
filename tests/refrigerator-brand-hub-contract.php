<?php
/** The refrigerator category aggregates recent refrigerator brand posts. */
declare(strict_types=1);
$root = dirname(__DIR__);
require_once $root . '/app/Support/refrigerator-brand-hub.php';
function brandHubAssert(bool $ok, string $message): void {
    if (!$ok) { fwrite(STDERR, "Refrigerator hub contract failed: $message\\n"); exit(1); }
}
brandHubAssert(aloIsRefrigeratorBrandHub(['name' => 'تعمیرات یخچال']), 'refrigerator parent is recognized');
brandHubAssert(aloIsRefrigeratorBrandHub(['name' => 'تعمیر یخچال']), 'alternate refrigerator title is recognized');
brandHubAssert(!aloIsRefrigeratorBrandHub(['name' => 'ال‌جی']), 'ordinary manufacturer does not become an aggregate hub');
brandHubAssert(!aloIsRefrigeratorBrandHub(['name' => 'تعمیرات ماشین لباسشویی']), 'unrelated category is not aggregated');
$hub = aloBrandFeedCriteria(5, true);
brandHubAssert(strpos($hub['where'], 'pb.status = 1') !== false, 'published filter is enforced');
brandHubAssert(strpos($hub['where'], 'pb.brand_id = ?') !== false, 'existing parent content retained');
brandHubAssert(substr_count($hub['where'], 'LIKE ?') === 4, 'refrigerator title and slug patterns included');
brandHubAssert($hub['values'] === [5, '%یخچال%', '%فریزر%', '%refrigerator%', '%fridge%'], 'prepared values match query');
$normal = aloBrandFeedCriteria(12, false);
brandHubAssert($normal['where'] === 'pb.status = 1 AND pb.brand_id = ?', 'other brands keep isolated listing');
brandHubAssert($normal['values'] === [12], 'other categories remain scoped to their id');
$source = file_get_contents($root . '/classes/app/Brands.php');
$view = file_get_contents($root . '/them/app/brands/categories.php');
brandHubAssert(strpos($source, 'aloBrandFeedCriteria($id, $isRefrigeratorHub)') !== false, 'controller uses hub filter');
brandHubAssert(strpos($source, 'COUNT(pb.id)') !== false, 'count is based on same filter');
brandHubAssert(strpos($source, 'pb.created_at DESC, pb.id DESC') !== false, 'recent refrigerator posts are ordered newest');
brandHubAssert(strpos($view, "linked_brand_name") !== false, 'cards show actual manufacturer');
echo "Refrigerator category cross-brand listing contracts passed\\n";
