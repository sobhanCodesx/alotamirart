<?php
declare(strict_types=1);
$root = dirname(__DIR__);
require_once $root . '/them/app/posts/article-contact-helper.php';
function checkContact(bool $yes, string $message): void {
    if (!$yes) { fwrite(STDERR, "Article contact test failed: $message\n"); exit(1); }
}
$custom = alo_article_contact(['contact_number' => '۰۹۱۲ ۳۴۵ ۶۷۸۹'], ['phon' => '02112345678']);
checkContact($custom['number'] === '۰۹۱۲ ۳۴۵ ۶۷۸۹', 'Use custom article number before author number');
checkContact($custom['tel'] === '09123456789', 'Normalize Persian digits in tel links');
checkContact($custom['custom'] === true, 'Mark article-specific contact');
$arabic = alo_article_contact(['contact_number' => '٠٩١٢-٣٤٥-٦٧٨٩'], []);
checkContact($arabic['tel'] === '09123456789', 'Normalize Arabic digits in tel links');
$fallback = alo_article_contact(['contact_number' => '  '], ['phon' => '021 12345678']);
checkContact($fallback['number'] === '021 12345678' && $fallback['tel'] === '02112345678' && !$fallback['custom'], 'Use author fallback when custom field blank');
$noContact = alo_article_contact([], []);
checkContact($noContact['number'] === '' && $noContact['tel'] === '', 'Do not show a call button when all numbers empty');
$invalid = alo_article_contact(['contact_number' => 'نامعتبر'], []);
checkContact($invalid['number'] === '' && $invalid['tel'] === '', 'No non-dialable phone links');
$source = file_get_contents($root . '/them/app/posts/post.php');
checkContact(strpos($source, "alo_article_contact(") !== false, 'Article template resolves post-specific phone');
checkContact(substr_count($source, 'htmlspecialchars($contactPhone,ENT_QUOTES') === 2, 'Inline card and sidebar show the selected phone');
checkContact(substr_count($source, 'htmlspecialchars($contactPhoneHref,ENT_QUOTES') === 3, 'Inline card, sidebar, mobile call links use the selected phone');
checkContact(strpos($source, '$authorPhoneHref') === false, 'Never use author-only tel link in article');
echo "Article contact contract passed\n";
