<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="ROBOTS" content="noindex,nofollow">
    <title>پنل مدیریت | افزودن پست</title>
    <!-- <script src="//cdn.ckeditor.com/4.18.0/full/ckeditor.js"></script> -->
    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
</div>
<div class="content-wrapper">
    <div class="container">
        <?php
        $message = flash('msg-post');
        if (!empty($message)) {
            ?>
            <div class="mb-2 alert alert-danger p-3"><p class="form-text text-white">
                    <?= $message ?>
                </p></div>
            <?php
        } ?>
        <form action="<?= assets('admin/brands/post/updated/' . $post['id']) ?>" method="POST"
              enctype="multipart/form-data">
            <label class="mt-2" for="">عنوان ها</label>
            <input type="text" value="<?= $post['title'] ?>" name="title" class="form-control mt-3"
                   placeholder="عنوان مرورگر">

            <label class="mt-2" for="">نوار آدرس url</label>
            <input type="text" value="<?= $post['slug'] ?>" name="slug" class="form-control mt-3" placeholder="عنوان نوار آدرس">
            <label class="mt-3" for="">چکیده</label>
            <input type="text" value="<?= $post['des'] ?>" name="des" class="form-control mt-3"
                   placeholder="خلاصه مطلب">
                   
                   
                               <div class="form-group">
                <label for="contact_number" class="mt-2">شماره تماس دلخواه</label>
                <input type="text" class="form-control mt-3" id="contact_number" name="contact_number" value="<?= htmlspecialchars($post['contact_number'] ?? '') ?>" placeholder="شماره تماس دلخواه (اختیاری)">
                <small class="form-text text-muted">اگر وارد نشود، شماره تماس پیش‌فرض کاربر نمایش داده می‌شود.</small>
            </div>

                   
            <label class="mt-3" for="">انتخاب تصویر</label>
            <input type="file" class="form-control mt-2" name="img" accept="image/*">
            <div class="mt-3">
                    <textarea class="mt-3" name="content" id="editor1">
                                <?= $post['content'] ?>
                    </textarea>
            </div>
          
            <label class="mt-2" for="">کلمات مرتبط</label>
            <input type="text" value="<?= $post['tags'] ?>" name="tags" placeholder="تگ ها" class="form-control mt-4">
            <label class="mt-2" for="">دسته بندی</label>
            <select name="brand_id" id="" class="form-control mt-3 mb-4">
                <?php foreach ($item as $m) { ?>
                    <option value="<?= $m['id'] ?>" <?= $post['brand_id'] == $m['id'] ? 'selected' : '' ?> ><?= $m['name'] ?></option>
                <?php } ?>
            </select>
            <input type="submit" class="form-control mt-3 mb-5 btn btn-success" value="ارسال">
        </form>
    </div>
</div>

</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>