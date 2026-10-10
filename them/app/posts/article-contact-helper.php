<?php
/** Choose a per-article contact number before the article author's profile number. */
if (!function_exists('alo_article_contact')) {
    function alo_article_contact(array $post, array $user = []): array
    {
        $custom = trim((string) ($post['contact_number'] ?? ''));
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
