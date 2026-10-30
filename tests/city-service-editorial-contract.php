<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Support/city-service-editorial.php';

function checkEditorial(bool $ok, string $reason): void {
    if (!$ok) throw new RuntimeException($reason);
}
$slugs = ['refrigerator', 'washing-machine', 'air-conditioner', 'tv', 'oven', 'dishwasher'];
$cityNames = [
  'tehran'=>'تهران', 'isfahan'=>'اصفهان', 'shiraz'=>'شیراز', 'ahvaz'=>'اهواز',
  'mashhad'=>'مشهد', 'babol'=>'بابل', 'sari'=>'ساری', 'amol'=>'آمل',
  'kashan'=>'کاشان', 'rasht'=>'رشت', 'rudsar'=>'رودسر', 'tabriz'=>'تبریز',
  'ardabil'=>'اردبیل', 'yazd'=>'یزد', 'qom'=>'قم', 'karaj'=>'کرج',
  'abadan'=>'آبادان', 'khorramshahr'=>'خرمشهر', 'dezful'=>'دزفول',
  'andimeshk'=>'اندیمشک', 'borujerd'=>'بروجرد', 'hormozgan'=>'هرمزگان',
  'behbahan'=>'بهبهان', 'semnan'=>'سمنان', 'shahrekord'=>'شهرکرد',
  'hamedan'=>'همدان', 'kermanshah'=>'کرمانشاه', 'urmia'=>'ارومیه',
  'qazvin'=>'قزوین', 'savadkuh'=>'سوادکوه', 'shushtar'=>'شوشتر',
  'gorgan'=>'گرگان', 'bojnord'=>'بجنورد', 'zanjan'=>'زنجان',
  'bandar-abbas'=>'بندرعباس', 'bushehr'=>'بوشهر', 'kerman'=>'کرمان',
  'neyshabur'=>'نیشابور', 'ilam'=>'ایلام', 'sanandaj'=>'سنندج',
];
checkEditorial(aloCityServiceEditorial(['name'=>'تهران','slug'=>'tehran'], 'bad-service') === null,
    'Unsupported service must not generate invented content');
checkEditorial(aloCityServiceEditorial(['name'=>'تهران','slug'=>'../../etc'], 'refrigerator') === null,
    'Invalid city slug must not become a link');
foreach ($slugs as $service) {
    $unique = [];
    foreach ($cityNames as $slug => $name) {
        $first = aloCityServiceEditorial(['name'=>$name, 'slug'=>$slug], $service);
        $second = aloCityServiceEditorial(['name'=>$name, 'slug'=>$slug], $service);
        checkEditorial(is_array($first) && $first === $second, 'Non-deterministic/missing city-service page ' . $slug . '/' . $service);
        checkEditorial(isset($first['html'], $first['meta_description'], $first['title']), 'Missing SEO fields');
        $html = $first['html'];
        checkEditorial(mb_strpos($html, $name) !== false, 'City name missing');
        checkEditorial(strlen($html) > 2200, 'Insufficient useful article text for ' . $slug . '/' . $service);
        checkEditorial(strpos($html, 'href="/services-in-' . $slug . '"') !== false,
            'Missing validated city related-services link');
        checkEditorial(strpos($html, '09123456789') === false && strpos($html, 'رضایت هزاران') === false &&
            strpos($html, 'از ۴۰۰,۰۰۰') === false, 'Old placeholder claims leaked');
        checkEditorial(strpos($html,'<h2>')!==false && strpos($html,'<h3>')!==false,
            'Article lacks useful heading hierarchy');
        checkEditorial(strlen($first['meta_description']) > 80, 'Missing actionable description');
        $normalized = str_replace($name, '[CITY]', $html);
        checkEditorial(!isset($unique[$normalized]), 'Two city pages reuse the same non-local content: ' . $unique[$normalized] . ' / ' . $slug . ' for ' . $service);
        $unique[$normalized] = $slug;
    }
}
$x = aloCityServiceEditorial(['name'=>'<script>alert(1)</script>','slug'=>'sample-city'], 'tv');
checkEditorial(strpos($x['html'],'<script>')===false && strpos($x['html'],'&lt;script&gt;')!==false,
    'City XSS did not escape');
echo "CITY_SERVICE_EDITORIAL_VERIFIED " . count($slugs) . " services, " . count($cityNames) .
    " cities each, distinct from mere city-name swaps\n";
