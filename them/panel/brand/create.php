<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <!-- <script src="//cdn.ckeditor.com/4.18.0/full/ckeditor.js"></script> -->
    <?php include BASE_PATH . '/them/admin/layout/head.php' ?>
    <title>نمایش پست - <?=$_SESSION['name'] ?></title>
</head>
<body>
<?php include BASE_PATH . '/them/panel/sidbar.php'; ?>
    <div class="content-wrapper">
        <div class="content-header">
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
    <form action="<?= assets('user/brand/created') ?>" method="POST" enctype="multipart/form-data">
             <label class="mt-3" for="">انتخاب تصویر</label>
            <input type="file" class="form-control mt-2" name="img" accept="image/*">
            <label class="mt-2" for="">عنوان ها</label>
            <input type="text" name="title" class="form-control mt-3" placeholder="عنوان مرورگر">
        <label class="mt-2" for="">نوار آدرس url <span class="text-danger">حتما انگلیسی وارد کنید</span></label>
            <input type="text" name="slug" class="form-control mt-3" placeholder="عنوان نوار آدرس">
            <label class="mt-3" for="">چکیده</label>
            <input type="text" name="des" class="form-control mt-3" placeholder="خلاصه مطلب">
            <div class="mt-3">
                    <textarea class="mt-3" name="content" id="editor1">
                        
                    </textarea>
            </div>
          
            <label class="mt-2" for="">کلمات مرتبط</label>
            <input type="text" name="tags" placeholder="تگ ها" class="form-control mt-4">
            <label class="mt-2" for="">دسته بندی</label>
            <select name="brand_id" id="" class="form-control mt-3 mb-4">
                <?php foreach ($item as $m) { ?>
                    <option value="<?= $m['id'] ?>"><?= $m['name'] ?></option>
                <?php } ?>
            </select>
            <input type="hidden" value="<?=$_SESSION['id'] ?>" name="user_id">
            <input type="submit" class="form-control mt-3 mb-5 btn btn-success" value="ارسال">
        </form> 
    </div>
        </div>
    </div>
    <?php include BASE_PATH . '/them/admin/layout/js.php'; ?>
</body>
</html>