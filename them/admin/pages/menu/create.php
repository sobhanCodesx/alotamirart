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
            <label class="mt-4" for="">افزودن منو</label>
            <form action="<?=assets('admin/menu/created')?>" method="post">
                <input type="text" name="title" placeholder="عنوان منو" class="form-control mt-3">
                <input type="text" name="description" placeholder="خلاصه منو" class="form-control mt-3">
                <input type="number" name="sort" class="form-control mt-3" placeholder="ترتیب">
                <input type="submit" value="ارسال" class="mt-3 form-control btn btn-success">
            </form>
        </div>
    </div>
</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>