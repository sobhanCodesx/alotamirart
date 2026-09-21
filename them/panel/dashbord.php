
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
                    <input require type="text" id="name" placeholder="نام و نام خانوادگی" name="name" class="form-control mt-4" value="<?=checkValue($aUser,'name')?>">
                    <input require type="text" placeholder="نام کاربری" name="user_name" class="form-control mt-4" value="<?=checkValue($aUser,'user_name')?>">
                    <input require type="text" placeholder="شماره موبایل" name="phon" class="form-control mt-4" value="<?=checkValue($aUser,'phon')?>">
                    <input require type="text" placeholder="ایمیل" name="email" class="form-control mt-4" value="<?=checkValue($aUser,'email')?>">
                    <label class="label-danger mt-2">پسورد شما : <?= checkValue($aUser,'password')?></label>
                    <input require type="password" placeholder="پسورد" name="password" class="form-control mt-1" value="<?=checkValue($aUser,'password')?>">
                    <div  class="bg-info mt-4 p-3 info-one">
                        <h3 class="text-center fs fs-5">تکمیل اطلاعات بیشتر</h3>
                        <textarea name="bio" class="form-control mt-4" placeholder="بیو گرافی" cols="20"><?=checkValue($aUser,'bio')?></textarea>
                        <input type="text" placeholder="عنوان کار شما" name="title" class="form-control mt-4" value="<?=checkValue($aUser,'title')?>">
                        <input type="text" placeholder="ادرس انیستاگرام" name="instagram" class="form-control mt-4" value="<?=checkValue($aUser,'instagram')?>">
                    </div>
                    <div style="display:none"  class="bg-primary mt-4 p-3 info-two">
                        <h3 class="text-center fs fs-5">اطلاعات فردی</h3>
                        <input type="text" placeholder="متولد" name="birt" class="form-control mt-4" value="<?=checkValue($aUser,'birt')?>">
                        <input type="text" placeholder="وب سایت" name="web" class="form-control mt-4" value="<?=checkValue($aUser,'web')?>">
                        <input type="text" placeholder="شهر شما" name="city" class="form-control mt-4" value="<?=checkValue($aUser,'city')?>">
                        <input type="text" placeholder="مدرک شما" name="crti" class="form-control mt-4" value="<?=checkValue($aUser,'crti')?>">
                        <input type="text" placeholder="سن" name="age" class="form-control mt-4" value="<?=checkValue($aUser,'age')?>">

                        <textarea name="address" class="form-control mt-4" placeholder="محل کار شما" cols="20"><?=checkValue($aUser,'address')?></textarea>
                    </div>

                    <button id="next-info" class="btn float-left btn-danger mt-3">اطلاعات بعدی</button>
                    <button id="after-info" class="btn btn-success mt-3">اطلاعات قبلی</button>
                    <br>
                    <br>
                    <br>
                    <br>
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
<script>
    $(document).ready(function() {
            $("#next-info").on("click", function(e) {
                e.preventDefault();
                $(".info-one").fadeOut(function() {
                    $(".info-two").fadeIn();
                });
            });
            $("#after-info").on("click", function(ev) {
                ev.preventDefault();
                $(".info-two").fadeOut(function() {
                    $(".info-one").fadeIn();
                });
            });
        });
</script>
