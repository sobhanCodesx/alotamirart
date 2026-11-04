<?php
declare(strict_types=1);
$base=dirname(__DIR__);
function v3assert(bool $ok,string $message):void{if(!$ok){fwrite(STDERR,"Detail V3 failed: ".$message."\n");exit(1);}}
$brand=file_get_contents($base.'/them/app/brands/post.php');
$article=file_get_contents($base.'/them/app/posts/post.php');
$css=file_get_contents($base.'/public/src/css/alo-detail-v3.css');
$js=file_get_contents($base.'/public/src/js/alo-detail-v3.js');
foreach([$brand,$article] as $view){
 v3assert(str_contains($view,'alo-detail-v3.css'), 'CSS loaded in both article templates');
 v3assert(str_contains($view,'alo-detail-v3.js'), 'sticky-bar visibility script loaded');
 v3assert(str_contains($view,'alo-author-showcase'),'author resume card present');
 v3assert(str_contains($view,'alo-mobile-resume'),'mobile resume link present');
 v3assert(str_contains($view,'alo-mobile-call'),'mobile call link present');
 v3assert(str_contains($view,'$authorProfile'),'author profile references actual user id and username');
 v3assert(str_contains($view,'detail-cover'),'featured image retained');
}
v3assert(str_contains($brand,'alo-brand-contact'), 'actual brand contact anchor');
v3assert(!str_contains($brand,'<div class="detail-owner">'), 'brand old author card removed');
v3assert(str_contains($brand,'مشاهده رزومه و مقالات'),'brand resume CTA');
v3assert(str_contains($css,'aspect-ratio:auto!important'),'image natural aspect ratio');
v3assert(str_contains($css,'margin-top:0!important'),'no image/content overlap');
v3assert(str_contains($css,'alo-author-visible'),'sticky bar hidden when owner card visible');
v3assert(str_contains($js,'IntersectionObserver'),'visible author section detected');
v3assert(str_contains($article,'$selectedContact'),'existing article-specific contact number selection preserved');
v3assert(str_contains($brand,'$contactHref'),'brand telephone selector preserved');
echo "Detail V3 article and brand author/resume contracts passed\n";
