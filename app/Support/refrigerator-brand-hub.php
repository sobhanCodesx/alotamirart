<?php
/** Shared refrigerator brand hub predicates. Other brand listings are unchanged. */
declare(strict_types=1);

function aloIsRefrigeratorBrandHub(array $brand): bool {
    $name = trim((string)($brand['name'] ?? ''));
    return $name === 'یخچال'
        || preg_match('/^تعمیر(?:ات)?\\s+یخچال(?:\\s|$)/u', $name) === 1;
}

/** @return array{where:string,values:array} */
function aloBrandFeedCriteria(int $brandId, bool $isRefrigeratorHub): array {
    if (!$isRefrigeratorHub) {
        return [
            'where' => 'pb.status = 1 AND pb.brand_id = ?',
            'values' => [$brandId],
        ];
    }
    // Build a cross-brand editorial index without reassigning each article's brand.
    return [
        'where' => 'pb.status = 1 AND (pb.brand_id = ? OR pb.title LIKE ? OR pb.title LIKE ? OR pb.slug LIKE ? OR pb.slug LIKE ?)',
        'values' => [$brandId, '%یخچال%', '%فریزر%', '%refrigerator%', '%fridge%'],
    ];
}
