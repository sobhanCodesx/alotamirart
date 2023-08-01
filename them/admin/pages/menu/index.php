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
    <div class="container">
        <table class="table mt-3">
            <thead>
            <tr>
                <th scope="col">ردیف</th>
                <th scope="col">عنوان</th>
                <th scope="col">وضعیت</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $count = 1;
            foreach ($menu as $c) { ?>
                <tr>
                    <th scope="row"><?= $count++ ?></th>
                    <td><?= $c['title'] ?></td>
                    <td><a class="btn btn-info"
                           href="<?= assets('admin/menu/update/' . $c['id']) ?>">اپدیت</a><a
                                class="btn btn-danger"
                                href="<?= assets('admin/menu/deleted/' . $c['id']) ?>">حذف</a></td>
                </tr>
            <?php }

            ?>
            </tbody>
        </table>
        <div class="container mt-4">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php echo get_pag($page, 'admin/menu/index', $pages, ''); ?>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>