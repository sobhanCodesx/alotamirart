<?php
declare(strict_types=1);
$root=dirname(__DIR__);
function resumeCheck(bool $yes,string $message): void {
    if(!$yes){fwrite(STDERR,"Article author resume contract failed: ".$message."\n");exit(1);}
}
$template=file_get_contents($root.'/them/app/posts/post.php');
$css=file_get_contents($root.'/public/src/css/alo-article-author.css');
$controller=file_get_contents($root.'/classes/app/Hom.php');
$router=file_get_contents($root.'/router/them.php');
resumeCheck(str_contains($template,'alo-author-showcase'), 'new author card should render');
resumeCheck(str_contains($template,'alo-article-author.css'), 'article-only stylesheet should load');
resumeCheck(str_contains($template,'مشاهده رزومه و مقالات'), 'resume CTA is missing');
resumeCheck(str_contains($template,'$authorProfile !=='), 'hide resume button if author profile is missing');
resumeCheck(str_contains($template,"assets('profile/'."), 'resume link is derived from the real author profile route');
resumeCheck(str_contains($template,"htmlspecialchars($authorProfile, ENT_QUOTES, 'UTF-8')"),'author profile URLs must be escaped');
resumeCheck(str_contains($router,"uri('profile/{name}/{id}'"), 'profile route not registered');
resumeCheck(str_contains($controller,"WHERE user_name = ? AND id = ?"), 'profile page must verify username and ID');
resumeCheck(str_contains($template,'$authorRole'), 'author role should use profile data');
resumeCheck(str_contains($template,'$authorBio'), 'author bio should use existing profile data');
resumeCheck(str_contains($template,'alo-mobile-resume'), 'resume CTA should be present in mobile sticky bar');
resumeCheck(str_contains($template,'alo-article-contact'), 'contact CTA should scroll to the real contact card');
resumeCheck(str_contains($css,'.detail-page--article .alo-author-showcase'), 'CSS must be scoped to articles');
resumeCheck(str_contains($css,'@media(max-width:620px)'), 'mobile style breakpoint missing');
resumeCheck(str_contains($template,'detail-cover-wrap'), 'existing featured image must remain');
resumeCheck(!str_contains($template,'href="#"'), 'buttons must not contain dummy links');
echo "Article author resume card, profile routing and mobile contact layout contract passed\n";
