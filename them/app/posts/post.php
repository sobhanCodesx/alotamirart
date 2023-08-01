<?php

ob_start("minifier");

function minifier($code)
{
    $search = array(

        '/\>[^\S ]+/s',
        '/[^\S ]+\</s',
        '/(\s)+/s',
        '/<!--(.|\s)*?-->/'
    );
    $replace = array('>', '<', '\\1');
    $code = preg_replace($search, $replace, $code);
    return $code;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="description" content="<?= $post['description'] ?>">
    <meta name="keywords" content="<?= $post['tags'] ?>">
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <link rel="icon" type="image/x-icon" href="<?= assets('public/src/img/logo.png') ?>">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $err == true ? $post['title'] : 'پست مورد نظر پیدا نشد' ?> - <?=$dataSeo['title']?></title>
</head>
<body>
<?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12 col-md-7">
            <?php if ($err) { ?>
                <div class="box-content-cities p-3">
                    <div class="size-img-city">
                        <img src='<?= assets($post['img']) ?>' class="size-img-city"
                             alt="<?= $post['title'] ?>"/>
                    </div>
                    <div class="alert alert-success mt-2 p-2">
                        <p>مدیریت : <span><?=$user['name'] ?></span></p>
                        <p>شماره تماس : <a class="text-decoration-none text-dark" href="tel:<?=$user['phon']?>"><?=$user['phon'] ?></a></p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
        </svg> <p class="d-inline text-danger m-1"><?=$view ?></p><span class="text-info">بازدید</span>
                        <?php if (isset($_SESSION['name'])) { ?>
                                <?php if ($_SESSION['role'] == 1) { ?>
                                    <button class="btn btn-success m-3"><a class="nav-link text-white" target="_blank" href="<?=assets('admin/posts/update/'.$post['id']) ?>">ویرایش</a></button>
                                    <?php }?>
                                <?php }?>
                    </div>
                    <h1 class="mt-3"><?= $post['title'] ?></h1>
                    <p><?= $post['content'] ?></p>
                </div>
            <?php } else { ?>
                <div class="p-2">
                    <h2 class="text-center alert-danger text-primary p-3">صفحه مورد نظر پیدا نشد</h2>
                </div>
            <?php } ?>
        </div>
        <div class="col-12 col-md-5">
            <?php require_once BASE_PATH . "/them/app/layout/sidbar.php" ?>
        </div>
    </div>
</div>


<br>
<?php require_once BASE_PATH . "/them/app/layout/footer.php" ?>
<?php require_once BASE_PATH . "/them/app/layout/js.php" ?>
</body>
</html>
<?php
ob_end_flush();
?> 