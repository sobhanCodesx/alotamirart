<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/Support/persian-typography.php';
function typeAssert(bool $ok, string $detail): void {
    if (!$ok) { fwrite(STDERR, "Persian typography contract failed: " . $detail . PHP_EOL); exit(1); }
}
$cases = [
    'تعمیر تلویزیون سامسونگ،ال جی,ایکس ویژن,سونی' =>
        'تعمیر تلویزیون سامسونگ، ال جی، ایکس ویژن، سونی',
    'مقالات آموزشی شامل:طراحی,سئو,تعمیرات و....' =>
        'مقالات آموزشی شامل: طراحی، سئو، تعمیرات و…',
    'این یک جمله است .جمله دوم' =>
        'این یک جمله است. جمله دوم',
    'قطعه اصلی، کیفیت مناسب. تعمیر ایمن.' =>
        'قطعه اصلی، کیفیت مناسب. تعمیر ایمن.',
    'نسخه 2.0 و عدد 100,000 و لینک https://example.com/a,b و info@example.com' =>
        'نسخه 2.0 و عدد 100,000 و لینک https://example.com/a,b و info@example.com',
];
foreach ($cases as $from => $to) {
    $actual = aloPersianTypography($from);
    typeAssert($actual === $to, "Mismatch: " . $from . " -> " . $actual);
    typeAssert(aloPersianTypography($actual) === $actual, "Idempotence failed: " . $from);
}
typeAssert(strpos(file_get_contents(dirname(__DIR__).'/index.php'),"aloPersianTypography(\$text)")!==false,
           'Global clean_display_text integration missing');
$home = file_get_contents(dirname(__DIR__).'/them/app/index.php');
typeAssert(strpos($home,'hreflang="fa-IR"')!==false, 'Persian homepage hreflang missing');
echo "Persian typography contract passed\n";
