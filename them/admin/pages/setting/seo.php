<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="ROBOTS" content="noindex,nofollow">
    <title>پنل مدیریت | ویرایش سئو سایت</title>
    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
</div>
<div class="content-wrapper">
    <div class="container pt-5">
        <form action="<?= assets('admin/settings/seo/create') ?>" method="post" enctype="multipart/form-data">
            <label class="mt-2">لوگو سایت وارد شود</label>
            <?php if (!empty($data['logo'])) { ?>
                <div class="container">
                    <img src="<?= assets($data['logo']) ?>" style="width: 10%;height: 10%">
                </div>
            <?php } ?>
            <?php if (!empty($data['id'])) { ?>
                <input type="hidden" name="id" value="<?php if (!empty($data['id'])) {
                    echo $data['id'];
                } ?>">
            <?php } ?>
            <input <?php if (empty($data['logo'])) { ?> required <?php } ?> type="file" name="logo"
                                                                            placeholder="لوگو سایت"
                                                                            class="mt-3 form-control" accept="image/*">
            <label class="mt-2">عنوان سایت وارد شود</label>
            <input required type="text" name="title" value="<?php if (!empty($data['title'])) {
                echo $data['title'];
            } ?>" placeholder="عنوان سایت" class="mt-3 form-control">
            <label class="mt-2">توضیحات سایت وارد شود</label>
            <input required type="text" name="description" value="<?php if (!empty($data['description'])) {
                echo $data['description'];
            } ?>" placeholder="توضیحات سایت" class="mt-3 form-control">
            <label class="mt-2">کلمات کلیدی سایت وارد شود</label>
            <input required type="text" name="keyword" value="<?php if (!empty($data['keyword'])) {
                echo $data['keyword'];
            } ?>" placeholder="کلمات کلیدی سایت" class="mt-3 form-control">

            <label class="mt-2">بنر سایت وارد شود</label>
            <?php if (!empty($data['logo'])) { ?>
                <div class="container">
                    <img src="<?= assets($data['header']) ?>" style="width: 30%;height: 30%">
                </div>
            <?php } ?>
            <input <?php if (empty($data['header'])) { ?> required <?php } ?> type="file" name="header" accept="image/*"
                                                                            placeholder="بنر سایت"
                                                                            class="mt-3 form-control">

            <label class="mt-2">h1 سایت وارد شود</label>
            <input required type="text" name="title_h1" value="<?php if (!empty($data['title_h1'])) {
                echo $data['title_h1'];
            } ?>" placeholder="h1 سایت" class="mt-3 form-control">

            <label class="mt-2">h2 سایت وارد شود</label>
            <input required type="text" name="title_h2" value="<?php if (!empty($data['title_h2'])) {
                echo $data['title_h2'];
            } ?>" placeholder="h2 سایت" class="mt-3 form-control">

            <label class="mt-2">بک گراند بالای منو</label>
            <?php if(!empty($data['bg_topheader'])){ ?>

                <div class="p-1" style="width:100%;background:<?=$data['bg_topheader']?>">
                    <p class="text-center text-danger">نتیجه</p>
                </div>

              <?php }?>
            <input required type="text" name="bg_topheader" value="<?php if (!empty($data['bg_topheader'])) {
                echo $data['bg_topheader'];
            } ?>" placeholder="بک گراند top header" class="mt-3 form-control">


      <label class="mt-2"> مشاوره سایت وارد شود </label>

            <textarea placeholder="مشاوره" class="mt-3 form-control" name="text_about" id="" cols="20" rows="10"><?php if (!empty($data['text_about'])) {
                echo $data['text_about'];
            } ?></textarea>
            

            <input type="submit" value="ارسال" class="form-control mt-3 btn btn-outline-success mb-3">
        </form>
    </div>
</div>
</body>
<?php
$message = flash('SuccessSeo');
if (!empty($message)) {
    ?>
    <script>
        Swal.fire(
            'Good job!',
            '<?= $message ?>',
            'success'
        )
    </script>
<?php } ?>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>
</html>
