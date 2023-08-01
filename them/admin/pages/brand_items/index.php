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
        <div class="container">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mt-5" data-bs-toggle="modal" data-bs-target="#exampleModal">
                افزودن برند جدید
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <label class="mt-4" for="">افزودن برند</label>
                                <form action="<?= assets('admin/brands/created') ?>" method="post"
                                      enctype="multipart/form-data">
                                    <input type="file" class="form-control mt-2" name="img" accept="image/*">
                                    <input type="text" name="name" placeholder="عنوان برند" class="form-control mt-3">
                                    <input type="text" name="des" placeholder="خلاصه برند" class="form-control mt-3">
                                    <input type="submit" value="ارسال" class="mt-3 form-control btn btn-success">
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

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
            foreach ($item as $c) { ?>
                <tr>
                    <th scope="row"><?= $count++ ?></th>
                    <td><?= $c['name'] ?></td>
                    <td><img src="<?= assets($c['img']) ?>" width="100" alt=""></td>
                    <td><a class="btn btn-info"
                           href="<?= assets('admin/brands/update/' . $c['id']) ?>">اپدیت</a><a
                                class="btn btn-danger"
                                href="<?= assets('admin/brands/delete/' . $c['id']) ?>">حذف</a></td>
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
                    <?php echo get_pag($page, 'admin/brands/index', $pages, ''); ?>
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