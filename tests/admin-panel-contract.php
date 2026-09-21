<?php

function adminAssert($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "Admin contract failed: " . $message . PHP_EOL);
        exit(1);
    }
}

$root = dirname(__DIR__);
$router = file_get_contents($root . '/router/admin.php');
$postClass = file_get_contents($root . '/classes/admin/posts/Post.php');
$create = file_get_contents($root . '/them/admin/pages/posts/create.php');
$update = file_get_contents($root . '/them/admin/pages/posts/update.php');
$layoutJs = file_get_contents($root . '/them/admin/layout/js.php');

adminAssert(strpos($create, "assets('admin/posts/created')") !== false, 'new article form must submit to created route');
adminAssert(strpos($create, 'name="user_id"') !== false, 'new article form must preserve author user_id');
adminAssert(strpos($router, "uri('admin/posts/create', 'Post', 'create', 'POST')") !== false, 'SEO analysis POST route must exist');
adminAssert(strpos($router, "admin/upload-image.php") !== false, 'CKEditor upload route must exist');
adminAssert(strpos($router, "admin/upload-image-ajax.php") !== false, 'AJAX image upload route must exist');
adminAssert(strpos($postClass, 'filterPostPayload') !== false, 'post writes must filter against actual posts columns');
adminAssert(strpos($postClass, "SHOW COLUMNS FROM posts") !== false, 'post payload must adapt to installed schema');
adminAssert(strpos($postClass, 'uploadImageAjax') !== false, 'AJAX image upload handler must exist');
adminAssert(strpos($postClass, 'uploadImageCkeditor') !== false, 'CKEditor upload handler must exist');
adminAssert(strpos($layoutJs, '!CKEDITOR.instances.editor1') !== false, 'CKEditor must not be initialized twice');
adminAssert(strpos($update, "assets('admin/posts/updated/' . \$post['id'])") !== false, 'edit form must submit to updated route');

foreach ([
    'classes/admin/menu/Menu.php',
    'classes/admin/brand/Items.php',
    'classes/admin/brand/BrandPosts.php',
    'classes/admin/link.php',
] as $file) {
    $content = file_get_contents($root . '/' . $file);
    adminAssert(strpos($content, 'AS total') !== false, basename($file) . ' pagination must use associative total alias');
}

echo "Admin panel contract passed" . PHP_EOL;
