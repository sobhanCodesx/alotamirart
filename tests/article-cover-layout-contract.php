<?php
declare(strict_types=1);
$root = dirname(__DIR__);
function checkArticleCover(bool $ok, string $message): void {
    if (!$ok) { fwrite(STDERR, "Article cover layout contract failed: $message\n"); exit(1); }
}
$css = file_get_contents($root . '/public/src/css/site.css');
$template = file_get_contents($root . '/them/app/posts/post.php');
checkArticleCover(str_contains($template, 'class="detail-cover-wrap"'), 'cover container missing');
checkArticleCover(str_contains($template, 'class="detail-cover"'), 'featured image missing');
checkArticleCover(str_contains($css, 'ALO ARTICLE COVER DISPLAY FIX 20261104'), 'mobile fix missing');
checkArticleCover(str_contains($css, '.detail-page--article .detail-cover-wrap .detail-cover'), 'cover fix must be scoped to articles');
checkArticleCover(str_contains($css, 'aspect-ratio:auto!important'), 'image must use natural aspect ratio');
checkArticleCover(str_contains($css, 'object-fit:contain!important'), 'image must not be cropped');
checkArticleCover(str_contains($css, 'height:auto!important'), 'image height must be automatic');
checkArticleCover(str_contains($css, '.detail-page--article .detail-body'), 'content styles must be scoped to articles');
checkArticleCover(str_contains($css, 'margin-top:0!important'), 'content must not overlap image');
checkArticleCover(str_contains($css, '.detail-page--article .detail-cover-wrap::after{display:none}'), 'overlay must not darken cover');
echo "Article cover layout contract passed\n";
