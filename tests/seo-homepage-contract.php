<?php
declare(strict_types=1);
$root=dirname(__DIR__);
function homepageAssert(bool $condition,string $message): void {
    if (!$condition) { fwrite(STDERR, "Homepage SEO contract failed: ".$message."\n"); exit(1); }
}
$index=file_get_contents($root.'/index.php');
homepageAssert(strpos($index,"\$incomingHost === 'alotamiratchi.ir'")===false, 'bare domain must never redirect to www');
homepageAssert(strpos($index,"'www.alotamiratchi.ir'")!==false, 'www to bare redirect condition missing');
homepageAssert(strpos($index,"header('Location: https://alotamiratchi.ir'")!==false, 'www to bare permanent redirect missing');
homepageAssert(strpos(file_get_contents($root.'/.htaccess'),'RewriteEngine On')!==false,'cPanel htaccess preserved');
$source=file_get_contents($root.'/them/app/index.php');
homepageAssert(strpos($source,'<meta property="og:image"')!==false,'OG image missing');
homepageAssert(strpos($source,'application/ld+json')!==false,'structured data missing');
homepageAssert(strpos(file_get_contents($root.'/them/app/layout/heading.php'),'apple-touch-icon')!==false,'Apple touch icon missing');
// Test against old advertising metadata to prove that public homepage stays focused on repairs.
define('BASE_PATH',$root);
$_SESSION=[];
function assets($path){return 'https://alotamiratchi.ir/'.ltrim($path,'/');}
function clean_display_text($v){return trim(strip_tags((string)$v));}
function excerpt_text($v,$n=25){return mb_substr(trim(strip_tags((string)$v)),0,80);}
function flash($k){return '';}
$dataSeo=['title'=>'الو تعمیراتچی','description'=>'تبلیغات گوگل','title_h1'=>'تبلیغات گوگل','title_h2'=>'تبلیغات گوگل','logo'=>'public/src/img/logo.png'];
$dataHeader=['title_one'=>'تبلیغات گوگل','title_two'=>'تبلیغات گوگل','title_tree'=>'تبلیغات گوگل'];
$dataFooter=['phon'=>'09933493049','email'=>'support@example.com','about_description'=>'','instagram'=>''];
$menu=[];
$post=[['id'=>490,'title'=>'چرا یخچال سرد نمی‌کند؟','img'=>'a.webp','content'=>'مطلب درباره تعمیر یخچال']];
$brands=[['id'=>377,'slug'=>'sample-brand-repair','title'=>'تعمیرات یخچال یک برند','img'=>'b.webp','content'=>'راهنمای تعمیر']];
$brand=[];
ob_start();
require $root.'/them/app/index.php';
$html=ob_get_clean();
$doc=new DOMDocument();
homepageAssert(@$doc->loadHTML('<?xml encoding="utf-8"?>'.$html)===true,'homepage HTML could not be parsed');
$xp=new DOMXPath($doc);
$title=$doc->getElementsByTagName('title')->item(0);
homepageAssert($title!==null && mb_strlen($title->textContent)>20 && strpos($title->textContent,'خدمات تعمیر لوازم خانگی')!==false,'SEO title short or off-topic');
homepageAssert($doc->getElementsByTagName('h1')->length===1,'homepage must have one H1');
$canonical=$xp->query('//link[@rel="canonical"]');
homepageAssert($canonical->length===1 && $canonical->item(0)->getAttribute('href')==='https://alotamiratchi.ir/','canonical mismatch');
$image=$xp->query('//meta[@property="og:image"]');
homepageAssert($image->length===1 && str_starts_with($image->item(0)->getAttribute('content'),'https://'),'absolute OG image missing');
$graphs=$xp->query('//script[@type="application/ld+json"]');
homepageAssert($graphs->length===1,'exactly one JSON-LD graph');
$schema=json_decode($graphs->item(0)->textContent,true);
homepageAssert(json_last_error()===JSON_ERROR_NONE && count($schema['@graph']??[])===2,'invalid Organization and WebSite schema');
homepageAssert($xp->query('//a[@href="https://alotamiratchi.ir/post/490"]')->length===1,'repeated internal article links');
homepageAssert($xp->query('//a[@href="https://alotamiratchi.ir/sample-brand-repair/377"]')->length===1,'repeated internal brand links');
$h3=[];foreach ($doc->getElementsByTagName('h3') as $node) $h3[]=trim($node->textContent);
homepageAssert(count($h3)===count(array_unique($h3)),'duplicate homepage H3');
homepageAssert(strpos($html,'تبلیغات گوگل')===false,'obsolete advertising copy in homepage');
$headings=$xp->query('//h1|//h2|//h3|//h4|//h5|//h6');
homepageAssert($headings->length<=16,'homepage excessive heading tags');
$missingAlt=$xp->query('//img[not(@alt) or normalize-space(@alt)=""]');
homepageAssert($missingAlt->length===0,'homepage image alt missing or empty');
$external=$xp->query('//a[starts-with(@href,"https://www.energy.gov/") and not(contains(concat(" ",normalize-space(@rel)," ")," nofollow "))]');
homepageAssert($external->length>=1,'relevant followed official external source missing');
homepageAssert(strpos($html,'support@example.com')===false && strpos($html,'mailto:')===false,'raw public email address exposed');
homepageAssert(strpos($html,base64_encode('support@example.com'))!==false,'click-to-email mechanism lost');
homepageAssert(strpos(file_get_contents($root.'/public/src/js/site.js'),'data-email-encoded')!==false,'email action handler not present');
$para=$xp->query('//p[contains(concat(" ",normalize-space(@class)," ")," seo-editorial-paragraph ")]');
homepageAssert($para->length===1,'editorial paragraph missing');
$plain=trim($para->item(0)->textContent);
$words=preg_split('/\\s+/u',$plain,-1,PREG_SPLIT_NO_EMPTY);
homepageAssert(count($words)>=60 && count($words)<=105,'main paragraph should be substantial but concise');
$sentences=preg_split('/[.!؟]+/u',$plain,-1,PREG_SPLIT_NO_EMPTY);
foreach ($sentences as $sentence) {
    $sentenceWords=preg_split('/\\s+/u',trim($sentence),-1,PREG_SPLIT_NO_EMPTY);
    homepageAssert(count($sentenceWords)<=20,'homepage editorial sentence too long');
}
homepageAssert(!preg_match('/\\s+[،.]/u',$plain),'space before punctuation');
homepageAssert(!preg_match('/[،.](?=\\S)/u',$plain),'punctuation stuck to next word');
echo "Homepage SEO contract passed\\n";
