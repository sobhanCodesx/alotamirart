<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="ROBOTS" content="noindex,nofollow">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>پنل مدیریت | ویرایش بنرها</title>
    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
</div>
<div class="content-wrapper">
    <div class="container p-5">
        <form action="<?= assets('admin/settings/header/create') ?>" method="post" enctype="multipart/form-data">
            <?php if (!empty($data['id'])) { ?>
                <input type="hidden" name="id" value="<?php if (!empty($data['id'])) {
                    echo $data['id'];
                } ?>">
            <?php } ?>
            <label class="mt-2">بنر اول سایت وارد شود</label>
            <?php if (!empty($data['img_one'])) { ?>
                <div class="container">
                    <img src="<?= assets($data['img_one']) ?>" style="width: 30%;height: 30%">
                </div>
            <?php } ?>
            <input <?php if (empty($data['img_one'])) { ?> required <?php } ?> type="file" name="img_one"
                                                                               accept="image/*"
                                                                               placeholder="بنر اول سایت"
                                                                               class="mt-3 form-control">

            <label class="mt-2">عنوان اول بنر وارد شود</label>
            <input required type="text" name="title_one" value="<?php if (!empty($data['title_one'])) {
                echo $data['title_one'];
            } ?>" placeholder="عنوان اول بنر" class="mt-3 form-control">

            <label class="mt-2">توضیحات اول بنر وارد شود</label>
            <input required type="text" name="description_one" value="<?php if (!empty($data['description_one'])) {
                echo $data['description_one'];
            } ?>" placeholder="عنوان اول بنر" class="mt-3 form-control">

            <br>
            <hr>
            <label class="mt-2">بنر دوم سایت وارد شود</label>
            <?php if (!empty($data['img_two'])) { ?>
                <div class="container">
                    <img src="<?= assets($data['img_two']) ?>" style="width: 30%;height: 30%">
                </div>
            <?php } ?>
            <input <?php if (empty($data['img_two'])) { ?> required <?php } ?> type="file" name="img_two"
                                                                               accept="image/*"
                                                                               placeholder="بنر دوم سایت"
                                                                               class="mt-3 form-control">
            <label class="mt-2">عنوان دوم بنر وارد شود</label>
            <input required type="text" name="title_two" value="<?php if (!empty($data['title_two'])) {
                echo $data['title_two'];
            } ?>" placeholder="عنوان دوم بنر" class="mt-3 form-control">

            <label class="mt-2">توضیحات دوم بنر وارد شود</label>
            <input required type="text" name="description_two" value="<?php if (!empty($data['description_two'])) {
                echo $data['description_two'];
            } ?>" placeholder="عنوان دوم بنر" class="mt-3 form-control">


            <br>
            <hr>
            <label class="mt-2">بنر سوم سایت وارد شود</label>
            <?php if (!empty($data['img_tree'])) { ?>
                <div class="container">
                    <img src="<?= assets($data['img_tree']) ?>" style="width: 30%;height: 30%">
                </div>
            <?php } ?>
            <input <?php if (empty($data['img_tree'])) { ?> required <?php } ?> type="file" name="img_tree"
                                                                                accept="image/*"
                                                                                placeholder="بنر سوم سایت"
                                                                                class="mt-3 form-control">
            <label class="mt-2">عنوان سوم بنر وارد شود</label>
            <input required type="text" name="title_tree" value="<?php if (!empty($data['title_tree'])) {
                echo $data['title_tree'];
            } ?>" placeholder="عنوان سوم بنر" class="mt-3 form-control">

            <label class="mt-2">توضیحات سوم بنر وارد شود</label>
            <input required type="text" name="description_tree" value="<?php if (!empty($data['description_tree'])) {
                echo $data['description_tree'];
            } ?>" placeholder="عنوان سوم بنر" class="mt-3 form-control">

            <input type="submit" value="ارسال" class="form-control mt-3 btn btn-outline-success mb-3">

        </form>
    </div>
</div>
<?php
$message = flash('SuccessHeader');
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
</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>
</html>
