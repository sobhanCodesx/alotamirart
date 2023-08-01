<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <?php include BASE_PATH . '/them/admin/layout/head.php' ?>
    <title>نمایش پست - <?=$_SESSION['name'] ?></title>
</head>
<body>
<?php include BASE_PATH . '/them/panel/sidbar.php'; ?>
    <div class="content-wrapper">
        <div class="content-header">
        <div class="container">
        <div class="alert alert-info">
            <h3><?=$_SESSION['name'] ?></h3>
            <p>نمایش مقاله های برند </p>
        </div>
    <table class="table mt-3">
        <thead>
        <tr>
            <th scope="col">ردیف</th>
            <th scope="col">عنوان</th>
            <th scope="col">عکس</th>
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
                <td><img src="<?= assets($c['img']) ?>" style="width:80px;height:100px" class="img-row-post"></td>
                <td><a class="btn btn-info" href="<?= $c['status'] == 0 ? assets('user/brand/update/'.$c['id']) : assets('post/brand/'.$c['id']) ?>"><?=$c['status'] == 0 ? 'آپدیت' : 'مشاهده مقاله'  ?></a><br>
                        <p class="<?=$c['status'] == 0 ? 'badge bg-warning' : 'badge bg-success' ?>"><?=$c['status'] == 0 ? 'درحال بررسی' : 'تایید شده' ?></p>
                    </td>
        <?php } ?>
        </tbody>
    </table>
    <div class="container mt-5 mb-3">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php echo get_pag($page,'user/brand', $pages, ''); ?>
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
        </div>
    </div>
    <?php include BASE_PATH . '/them/admin/layout/js.php'; ?>
</body>
</html>
