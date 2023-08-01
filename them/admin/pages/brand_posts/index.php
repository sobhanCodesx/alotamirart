<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="ROBOTS" content="noindex,nofollow">
    <title>پنل مدیریت | ویرایش منو</title>
    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
</div>
<div class="content-wrapper">
    <table class="table mt-3">
        <thead>
        <tr>
            <th scope="col">ردیف</th>
            <th scope="col">عنوان</th>
            <th scope="col">عکس</th>
            <th scope="col">نویسنده</th>
            <th scope="col">موقعیت</th>
            <th scope="col">وضعیت</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        foreach ($post as $c) { ?>
            <tr>
                <th scope="row"><?= $count++ ?></th>
                <td><?= $c['title'] ?></td>
                <td><img src="<?= assets($c['img']) ?>" class="img-row-post"></td>
                <td><?=$c['w']?></td>
                <td><a class="<?=$c['status'] == 1 ? 'btn btn-danger' : 'btn btn-success' ?>" href="<?= assets('status/brand/'.$c['id']) ?>"><?=$c['status'] == 1 ? 'غیرفعال کن' : 'فعال کن' ?></a></td>
                <td><a class="btn btn-info" href="<?= assets('admin/brands/post/update/' . $c['id']) ?>">اپدیت</a><a
                            class="btn btn-danger" href="<?= assets('admin/brands/post/delete/' . $c['id']) ?>">حذف</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="container">
        <?php echo get_pag($page, 'admin/brands/post', $pages, ''); ?>
    </div>
</div>

</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>