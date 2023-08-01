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
            <form action="<?= assets('admin/posts/updated/'.$post['id']) ?>" method="POST" enctype="multipart/form-data">
                <label class="mt-2" for="">عنوان ها</label>
                <input value="<?=$post['title'] ?>" type="text" name="title" class="form-control mt-3" placeholder="عنوان مرورگر">
                <label class="mt-3" for="">چکیده</label>
                <input value="<?=$post['description'] ?>" type="text" name="description" class="form-control mt-3" placeholder="خلاصه مطلب">
                <label class="mt-3" for="">انتخاب تصویر</label>
                <input type="file" class="form-control mt-2" name="img" accept="image/*">
                <div class="mt-3">
                    <textarea class="mt-3" name="content" id="editor1">
                     <?=$post['content'] ?>
                    </textarea>
                </div>
              
                <label class="mt-2" for="">کلمات مرتبط</label>
                <input type="text" value="<?=$post['tags'] ?>" name="tags" placeholder="تگ ها" class="form-control mt-4">
                <label class="mt-2" for="">دسته بندی</label>
                <select name="post_id" id="" class="form-control mt-3 mb-4">
                    <?php foreach ($menus as $m) { ?>
                        <option value="<?= $m['id'] ?>" <?php if($post['post_id'] == $m['id']){ ?>selected<?php } ?> ><?= $m['title'] ?></option>
                    <?php } ?>
                </select>
                <input type="submit" class="form-control mt-3 mb-5 btn btn-success" value="ارسال">
            </form>
        </div>
    </div>

</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>