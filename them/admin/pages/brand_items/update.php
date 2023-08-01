<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="ROBOTS" content="noindex,nofollow">
    <title>پنل مدیریت | افزودن منو</title>
    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
</div>
<div class="content-wrapper">
    <div class="container">
        <label class="mt-4" for="">افزودن برند</label>
        <form action="<?= assets('admin/brands/updated/'.$item['id']) ?>" method="post"
              enctype="multipart/form-data">
            <input type="file" class="form-control mt-2" name="img" accept="image/*">
            <input type="text" value="<?=$item['name']?>" name="name" placeholder="عنوان برند" class="form-control mt-3">
            <input type="text" value="<?=$item['des']?>" name="des" placeholder="خلاصه برند" class="form-control mt-3">
            <input type="submit" value="ارسال" class="mt-3 form-control btn btn-success">
        </form>
    </div>
</div>
</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>