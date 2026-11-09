<?php
/** Narrow contact override for heating appliances in two cities. */
if (!function_exists('alo_chalus_nowshahr_heat_contact')) {
    function alo_chalus_nowshahr_heat_contact(string $title): bool
    {
        $normalized = str_replace(['ي', 'ك', '‌'], ['ی', 'ک', ' '], $title);
        $city = preg_match('/(?<![\p{L}\p{N}])(?:چالوس|نوشهر)(?![\p{L}\p{N}])/u', $normalized);
        $service = preg_match('/(?:پکیج|پکیچ|اجاق\s*گاز|گاز\s*رومیزی)/u', $normalized);
        return $city === 1 && $service === 1;
    }
}
require_once BASE_PATH . '/api/eight-gilan-city-contacts.php';
/** Choose a per-article contact number before the article author's profile number. */
if (!function_exists('alo_article_contact')) {
    function alo_article_contact(array $post, array $user = []): array
    {
        $custom = aloEightCityMatch((string)($post['title'] ?? '')) ? '09108731535' : (alo_chalus_nowshahr_heat_contact((string)($post['title'] ?? ''))
            ? '09901068051' : trim((string) ($post['contact_number'] ?? '')));
        $author = trim((string) ($user['phon'] ?? ''));
        $number = $custom !== '' ? $custom : $author;
        $ascii = strtr($number, [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
        ]);
        $digits = preg_replace('/[^0-9]/', '', $ascii);
        if ($digits === '') {
            return ['number' => '', 'tel' => '', 'custom' => false];
        }
        $tel = (str_starts_with(ltrim($ascii), '+') ? '+' : '') . $digits;
        return ['number' => $number, 'tel' => $tel, 'custom' => $custom !== ''];
    }
}
