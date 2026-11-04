<?php
/** Paging helpers for profile posts and brand content. No external state. */
declare(strict_types=1);

function aloProfileVisiblePages(int $current, int $total): array
{
    if ($total <= 1) return [];
    $current = min($total, max(1, $current));
    $pages = [1, $total];
    for ($n = max(1, $current - 2); $n <= min($total, $current + 2); $n++) {
        $pages[] = $n;
    }
    $pages = array_values(array_unique($pages));
    sort($pages, SORT_NUMERIC);
    $result = [];
    $last = 0;
    foreach ($pages as $n) {
        if ($last && $n > $last + 1) $result[] = null;
        $result[] = $n;
        $last = $n;
    }
    return $result;
}

function aloProfileDigits(int $number): string
{
    return strtr((string)$number, '0123456789', '۰۱۲۳۴۵۶۷۸۹');
}

function aloProfilePageHref(string $base, int $postsPage, int $brandsPage, string $section): string
{
    $anchor = $section === 'profile-brands' ? 'profile-brands' : 'profile-posts';
    return $base . '?page=' . max(1, $postsPage)
        . '&brand_page=' . max(1, $brandsPage) . '#' . $anchor;
}
