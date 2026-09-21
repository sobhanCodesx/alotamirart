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
            <th scope="col">نام</th>
            <th scope="col">شماره تماس</th>
            <th scope="col">نام کاربری</th>
            <th scope="col">پروفایل</th>
            <th scope="col">سطح دسترسی</th>
            <th scope="col">وضعیت</th>
            <th scope="col">حذف</th>
        </tr>
        </thead>
        <tbody>
            
        <?php
        $count = 1;
        foreach ($users as $c) { ?>
            <tr>
                <th scope="row"><?= $count++ ?></th>
                <td><?= $c['name'] ?></td>
                <td><?= $c['phon'] ?></td>
                <td><?= $c['user_name'] ?></td>
                <td>
                    <button class="btn btn-info"><a class="text-dark" target="__blank" href="<?= assets('profile/'. $c['user_name'] .'/'. $c['id']) ?>" >پروفایل</a></button>
                    <button class="btn btn-success"><a class="text-dark"  href="<?= assets('inner/user/'. $c['id']) ?>" >ورود با این کاربر</a></button>
                </td>
                <td><a class="<?=$c['writer'] == 1 ? 'btn btn-danger' : 'btn btn-success' ?>" href="<?=assets('user/status/'.$c['id'])?>"><?= $c['writer'] == 1 ? 'غیر نویسنده' : 'نویسنده' ?></a></td>
                <td><a class="<?= $c['role'] == 1 ? 'btn btn-success' : 'btn btn-primary' ?>"
                       href="<?= assets('admin/users/status/' . $c['id']) ?>"><?= $c['role'] == 1 ? 'ادمین' : 'کاربر ' ?></a>
                </td>
                <td><a class="btn btn-danger" href="<?= assets('admin/users/delete/' . $c['id']) ?>">حذف</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="container">
        <?php echo get_pag($page, 'admin/users', $pages, ''); ?>
    </div>
</div>
</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>