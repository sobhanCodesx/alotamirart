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
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="<?= $post['des'] ?>">
    <meta name="keywords" content="<?= $post['tags'] ?>">
    <meta property="og:title" content="<?= $err == true ? $post['title'] : 'پست مورد نظر پیدا نشد' ?>">
    <meta property="og:description"
          content="<?= $err == true ? $post['des'] : 'پست مورد نظر پیدا نشد' ?>">
    <meta property="og:image" content="<?= $err == true ? $post['img'] : '' ?>">
    <meta property="og:site_name" content="<?=$dataSeo['title']?>">
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
                    <div class="mt-5">
                        <i class="fa fa-user-circle-o" aria-hidden="true"></i> : <?= $user['name'] ?> <br>
                        <i class="fas fa-phone mt-3"></i> : <a class="text-danger" style="text-decoration: none"
                                                               href="tel:<?= $user['phon'] ?>"><?= $user['phon'] ?></a>
                                                               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                                <br>
                                                                <i class="fa fa-eye" aria-hidden="true"></i><p class="d-inline text-danger m-1"> <?=$view ?> </p><span class="text-info">بازدید</span>

                        <br>
                    </div>
                    <div class="alert-success mt-4">
                        <p class="p-3">جهت ارتباط با کارشناسان تعمیرات برند <?= $post['namebrand'] ?> با  شماره
                            های بالا تماس حاصل
                            فرمایین </p>
                            <?php if (isset($_SESSION['name'])) { ?>
                                <?php if ($_SESSION['role'] == 1) { ?>
                                    <button class="btn btn-success m-3"><a class="nav-link text-white" target="_blank" href="<?=assets('admin/brands/post/update/'.$post['id']) ?>">ویرایش</a></button>
                                    <?php }?>
                                <?php }?>
                            
                    </div>
                    <h1 class="mt-5"><?= $post['title'] ?></h1>
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