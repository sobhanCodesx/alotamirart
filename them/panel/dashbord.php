<div>
    <div class="container mt-5 m-2">
        <h1 class="text-center container fs  fs-3 text-info">پنل کاربری</h1>
        <form  action="<?=assets('update/profile/'.$aUser['id']) ?>" method="POST" enctype="multipart/form-data">
            <div style="margin: auto" class=" justify-content-center align-items-center mt-3">
                <?php if (empty($aUser['img'])){ ?>
                <img style="border-radius: 30%" src="<?= assets('them/admin/dist/img/avatar.png') ?>" alt="">
                <?php }else{ ?>
                    <img style="border-radius: 30%;width: 20%" src="<?= assets($aUser['img']) ?>" alt="">
                <?php } ?>
                    <input type="file" name="img" class="form-control mt-4" accept="image/*">
                <div class="container">
                    <input type="text" placeholder="نام و نام خانوادگی" name="name" class="form-control mt-4" value="<?=checkValue($aUser,'name')?>">
                    <input type="text" placeholder="نام کاربری" name="user_name" class="form-control mt-4" value="<?=checkValue($aUser,'user_name')?>">
                    <input type="text" placeholder="ایمیل" name="email" class="form-control mt-4" value="<?=checkValue($aUser,'email')?>">
                    <label class="label-danger mt-2">پسورد شما : <?= checkValue($aUser,'password')?></label>
                    <input type="password" placeholder="پسورد" name="password" class="form-control mt-1" value="<?=checkValue($aUser,'password')?>">
                    <div class="bg-info mt-4 p-3">
                        <h3 class="text-center fs fs-5">تکمیل اطلاعات بیشتر</h3>
                        <textarea name="bio" class="form-control mt-4" placeholder="بیو گرافی" cols="20"><?=checkValue($aUser,'bio')?></textarea>
                        <input type="text" placeholder="عنوان کار شما" name="title" class="form-control mt-4" value="<?=checkValue($aUser,'title')?>">
                        <input type="text" placeholder="ادرس انیستاگرام" name="instagram" class="form-control mt-4" value="<?=checkValue($aUser,'instagram')?>">
                    </div>
                    <button type="submit" class="btn btn-outline-success mt-3 form-control">ارسال</button>
                </div>
            </div>
        </form>

    </div>
</div>
<?php
$message = flash('msg');
if (!empty($message)) {
    ?>
    <script>
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?=$message ?>!',
        })
    </script>
    <?php
} ?>
<?php
$message = flash('saveuser');
if (!empty($message)) {
    ?>
    <script>
        Swal.fire({
        title: '<?= $message ?>',
        width: 600,
        padding: '3em',
        color: '#716add',
        background: '#fff url(<?=assets("public/src/img/trees.png") ?>)',
        backdrop: `
        rgba(0,0,123,0.4)
        url()
        left top
        no-repeat
        `
        })
    </script>
<?php }   ?>
