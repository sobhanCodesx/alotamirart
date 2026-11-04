<?php
declare(strict_types=1);
$root=dirname(__DIR__);
require_once $root.'/app/Support/profile-pagination.php';
function pageCheck(bool $value,string $message): void {
  if(!$value){fwrite(STDERR,"Profile pagination contract failed: ".$message."\n");exit(1);}
}
pageCheck(aloProfileVisiblePages(1,1)===[],'single page should not render numbers');
pageCheck(aloProfileVisiblePages(1,4)===[1,2,3,4],'four pages are all visible');
pageCheck(aloProfileVisiblePages(5,12)===[1,null,3,4,5,6,7,null,12],'middle pages show gaps');
pageCheck(aloProfileDigits(203)==='۲۰۳','Persian digits expected');
pageCheck(aloProfilePageHref('https://alotamiratchi.ir/profile/demo/5',2,3,'profile-posts')==='https://alotamiratchi.ir/profile/demo/5?page=2&brand_page=3#profile-posts','posts query preserves brands page');
pageCheck(aloProfilePageHref('https://alotamiratchi.ir/profile/demo/5',2,3,'profile-brands')==='https://alotamiratchi.ir/profile/demo/5?page=2&brand_page=3#profile-brands','brand query preserves posts page');
$controller=file_get_contents($root.'/classes/app/Hom.php');
$profile=file_get_contents($root.'/them/app/profile/index.php');
pageCheck(substr_count($controller,'SELECT COUNT(id) FROM posts WHERE user_id = ? AND status = 1')===1,'must count author articles');
pageCheck(substr_count($controller,'SELECT COUNT(id) FROM post_brand WHERE user_id = ? AND status = 1')===1,'must count author brand content');
pageCheck(str_contains($controller,'$postsOffset')&&str_contains($controller,'$brandsOffset'),'database offset must reflect page numbers');
pageCheck(str_contains($controller,'$_GET[\'page\']')&&str_contains($controller,'$_GET[\'brand_page\']'),'both page selectors must be separate');
pageCheck(substr_count($profile,'aloProfileVisiblePages(')===2,'both article and brand lists must have numbered navigation');
pageCheck(str_contains($profile,'aria-current="page"')&&str_contains($profile,'aria-label="صفحه'),'accessible page numbering');
pageCheck(str_contains($profile,'id="profile-posts"')&&str_contains($profile,'id="profile-brands"'),'navigation anchors');
echo "Profile author article and brand pagination contracts passed\n";
