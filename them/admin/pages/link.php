<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="ROBOTS" content="noindex,nofollow">
    <title>پنل مدیریت | ویرایش لینک </title>
    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
</div>
<div class="content-wrapper">
    <!-- ////////////////////// link create -->
    <div class="container mt-3">
      <!-- Button trigger modal -->
<button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
  افزودن لینک
</button>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form action="<?=assets('backlink/admin/create')?>" method="post">
                <input type="text" class="form-control mt-2" placeholder="عنوان لینک" name="title">
                <input type="text" class="form-control mt-2" placeholder="لینک" name="link">
                <input type="submit" class="btn btn-success mt-2" value="ارسال">
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

    </div>
    <!-- ////////////////////////////// link create -->
    <table class="table mt-3">
        <thead>
        <tr>
            <th scope="col">ردیف</th>
            <th scope="col">لینک</th>
            <th scope="col">حذف</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        foreach ($link as $c) { ?>
            <tr>
                <th scope="row"><?= $count++ ?></th>
                <td><?= $c['title'] ?></td>
                <td><a class="btn btn-danger" href="<?= assets('delete/backlink/' . $c['id']) ?>">حذف</a></td>
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