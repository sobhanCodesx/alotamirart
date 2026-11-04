<?php
declare(strict_types=1);
$root=dirname(__DIR__);
function exploreAssert(bool $test,string $message): void {
  if(!$test){fwrite(STDERR,"Explore preview contract failed: ".$message."\n");exit(1);}
}
$css=file_get_contents($root.'/public/src/css/alo-explore.css');
$js=file_get_contents($root.'/public/src/js/alo-explore.js');
$head=file_get_contents($root.'/them/app/layout/heading.php');
$foot=file_get_contents($root.'/them/app/layout/js.php');
$home=file_get_contents($root.'/them/app/index.php');
exploreAssert(str_contains($head,'alo-explore.css'),'new stylesheet is loaded on every public page');
exploreAssert(str_contains($foot,'alo-explore.js'),'deferred script is loaded on every public page');
exploreAssert(str_contains($css,'.alo-home-premium .alo-brand-card__media img'),'brand images have dedicated CSS rule');
exploreAssert(str_contains($css,'.content-grid .content-card__media img'),'article images have dedicated CSS rule');
exploreAssert(str_contains($css,'object-fit:contain !important'),'cover does not crop the main card imagery');
exploreAssert(str_contains($css,'.alo-explore__image')&&str_contains($css,'object-fit:contain'),'preview shows full image');
exploreAssert(str_contains($js,'createElement("dialog")')&&str_contains($js,'showModal()'),'opens a native, accessible preview');
exploreAssert(str_contains($js,'aria-labelledby')&&str_contains($js,'aria-haspopup'),'dialog accessibility signals');
exploreAssert(str_contains($js,'event.ctrlKey')&&str_contains($js,'event.metaKey'),'modified link clicks are not hijacked');
exploreAssert(str_contains($js,'event.preventDefault()'),'primary link click is previewed');
exploreAssert(str_contains($js,'event.key==="ArrowLeft"')&&str_contains($js,'touchend'),'keyboard/swipe support');
exploreAssert(str_contains($js,'url.origin!==window.location.origin'),'external links are not hijacked');
exploreAssert(str_contains($js,'textContent=item.title'),'untrusted article text is not injected as HTML');
exploreAssert(str_contains($js,'[data-explore-open]')&&str_contains($js,'item.url'),'full-page link remains usable');
exploreAssert(str_contains($home,"assets('post/' . (int)\$item['id'])"),'canonical article links preserved');
exploreAssert(str_contains($home,"assets('brands/categories/' . (int)\$item['id'] . '/1')"),'canonical brand links preserved');
echo "Explore preview and uncropped card contract passed\n";
