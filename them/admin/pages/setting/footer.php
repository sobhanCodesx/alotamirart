<!DOCTYPE html>
<html lang="fa">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="ROBOTS" content="noindex,nofollow">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <title>پنل مدیریت | ویرایش فوتر</title>
        <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
    </head>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
        </div>
        <div class="content-wrapper">
            <div class="container pt-5">
                <form action="<?= assets('admin/settings/footer/create') ?>" method="post" enctype="multipart/form-data">

                    <label class="mt-2">عنوان فوتر سایت وارد شود</label>
                    <input required type="text" name="about_title" value="<?php if (!empty($data['about_title'])) {
                        echo $data['about_title'];
                    } ?>" placeholder="عنوان فوتر سایت" class="mt-3 form-control">

                    <label class="mt-2">توضیحات فوتر سایت وارد شود</label>

                    <textarea class="mt-3 form-control" placeholder="توضیحات فوتر سایت" required name="about_description"><?php if (!empty($data['about_description'])) { echo $data['about_description']; } ?></textarea>
                    <label class="mt-2">ایمیل فوتر سایت وارد شود</label>
                    <input required type="email" name="email" value="<?php if (!empty($data['email'])) {
                        echo $data['email'];
                    } ?>" placeholder="ایمیل فوتر سایت" class="mt-3 form-control">


                    <label class="mt-2">موبایل فوتر سایت وارد شود</label>
                    <input required type="text" name="phon" value="<?php if (!empty($data['phon'])) {
                        echo $data['phon'];
                    } ?>" placeholder="موبایل فوتر سایت" class="mt-3 form-control">

                    <label class="mt-2">انیستاگرام فوتر سایت وارد شود</label>
                    <input required type="text" name="instagram" value="<?php if (!empty($data['instagram'])) {
                        echo $data['instagram'];
                    } ?>" placeholder="انیستاگرام فوتر سایت" class="mt-3 form-control">

                    <?php if (!empty($data['id'])) { ?>
                        <input type="hidden" name="id" value="<?php if (!empty($data['id'])) {
                            echo $data['id'];
                        } ?>">
                    <?php } ?>
                    <label class="mt-2">  بک گراند فوتر سایت وارد شود</label>
                    <?php if (!empty($data['img_footer'])) { ?>
                        <div class="container">
                            <img src="<?= assets($data['img_footer']) ?>" style="width: 30%;height: 30%">
                        </div>
                    <?php } ?>
                    <input <?php if (empty($data['img_footer'])) { ?> required <?php } ?> type="file" name="img_footer"
                                                                                       accept="image/*"
                                                                                       placeholder="بک گراند فوتر"
                                                                                       class="mt-3 form-control">

                    <input type="submit" value="ارسال" class="form-control mt-3 btn btn-outline-success mb-3">

                </form>
            </div>
        </div>
    </body>
    <?php
    $message = flash('SuccessFooter');
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
