<?php
declare(strict_types=1);
$root=dirname(__DIR__);
function seoAssert(bool $ok,string $message): void {
    if (!$ok) { fwrite(STDERR, "SEO crawl contract failed: $message\n"); exit(1); }
}
$robots=file_get_contents($root.'/robots.txt');
seoAssert(strpos($robots,'damavandservice.com')===false, 'old unrelated domain in robots');
foreach (['post/sitemap','brand/sitemap'] as $path) {
    seoAssert(strpos($robots,'Sitemap: https://alotamiratchi.ir/'.$path)!==false, 'own sitemap missing in robots: '.$path);
}
function renderSeoSitemap(string $file,array $entries): string {
    $get=$entries;
    ob_start();
    require $file;
    return (string)ob_get_clean();
}
seoAssert(class_exists('DOMDocument'),'XML DOM extension is required for sitemap validation');
$postXml=renderSeoSitemap($root.'/mappost/sitemap.php',[
    ['id'=>490,'created_at'=>'2025-09-20 12:00:00','updated_at'=>'2026-09-30 15:30:00'],
    ['id'=>491,'created_at'=>'2026-10-01 08:00:00'],
]);
$dom=new DOMDocument();
seoAssert(@$dom->loadXML($postXml)===true,'articles sitemap malformed');
$urls=$dom->getElementsByTagName('url');
seoAssert($urls->length===2, 'articles sitemap count');
seoAssert(trim($urls->item(0)->getElementsByTagName('loc')->item(0)->textContent)==='https://alotamiratchi.ir/post/490','canonical article URL');
seoAssert(trim($urls->item(0)->getElementsByTagName('lastmod')->item(0)->textContent)==='2026-09-30','article lastmod uses real edit date, not current date');
$brandXml=renderSeoSitemap($root.'/mapbrand/sitemap.php',[
    ['id'=>377,'slug'=>'representative-air-conditioner-gree-mahshahr','created_at'=>'2024-10-01'],
    ['id'=>378,'slug'=>'','created_at'=>'2024-11-01'],
]);
$domBrand=new DOMDocument();
seoAssert(@$domBrand->loadXML($brandXml)===true,'brand sitemap malformed');
$brands=$domBrand->getElementsByTagName('url');
seoAssert($brands->length===1, 'exclude brand records with no valid route slug');
seoAssert(trim($brands->item(0)->getElementsByTagName('loc')->item(0)->textContent)==='https://alotamiratchi.ir/representative-air-conditioner-gree-mahshahr/377','brand sitemap path');
$postSource=file_get_contents($root.'/them/app/posts/post.php');
$brandSource=file_get_contents($root.'/them/app/brands/post.php');
seoAssert(strpos($postSource,'rel="canonical" href="https://alotamiratchi.ir/post/')!==false,'article canonical link');
seoAssert(strpos($brandSource,'rel="canonical" href="https://alotamiratchi.ir/')!==false,'brand canonical link');
echo "SEO crawling and canonicalization checks passed\n";
