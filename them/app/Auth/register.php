<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once BASE_PATH . "/them/app/layout/heading.php" ?>
    <link rel="stylesheet" href="<?= assets('public/src/css/auth.css') ?>">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600' rel='stylesheet'
          type='text/css'>
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
    <title>ثبت نام</title>
</head>
<body>
<?php require_once BASE_PATH . "/them/app/layout/header.php" ?>

<div class="container mt-3">
    <div class="testbox">
        <h1>ثبت نام</h1>
        <form action="<?= assets('registered') ?>" method="POST">
            <label id="icon" class="box-icon" for="name"><i class="icon-user icon-reg"></i></label>
            <input type="text" value="<?php if(!empty($_SESSION['name_temp'])){ echo getByUser('name_temp'); ?><?php }?>" class="box-icon p-2" name="name" id="name" placeholder="نام و نام خانوادگی" required/>
            <label id="icon" class="box-icon" for="user_name"><i class="fa fa-address-card icon-reg"></i></label>
            <input type="text" value="<?php if(!empty($_SESSION['user_name_temp'])){ echo getByUser('user_name_temp'); ?><?php }?>" class="box-icon p-2" name="user_name" id="name" placeholder="نام کاربری"/ required>
            <label id="icon" class="box-icon" for="email"><i class="icon-envelope  icon-reg"></i></label>
            <input type="text" value="<?php if(!empty($_SESSION['email_temp'])){ echo getByUser('email_temp'); ?><?php }?>" class="p-2" name="email" id="name" placeholder="ایمیل"/ required>
            <label id="icon" class="box-icon" for="phon"><i class="fa fa-phone icon-reg"></i></label>
            <input type="text" value="<?php if(!empty($_SESSION['phon_temp'])){ echo getByUser('phon_temp'); ?><?php }?>" class="p-2" name="phon" id="name" placeholder="شماره تماس"/ required>
            <label id="icon" for="name"><i class="icon-shield icon-reg"></i></label>
            <input type="password" value="<?php if(!empty($_SESSION['password_temp'])){ echo getByUser('password_temp'); ?><?php }?>" class="p-2" name="password" id="name" placeholder="پسورد"/ required>
            <label id="icon" for="name"><i class="icon-shield icon-reg"></i></label>
            <input type="password" class="p-2" name="password_two" placeholder="تکرار پسورد"/ required>
            <input type="submit" value="ارسال" class="btn btn-success">
        </form>
    </div>
</div>
<?php require_once BASE_PATH . "/them/app/layout/footer.php" ?>
<?php require_once BASE_PATH . "/them/app/layout/js.php" ?>
<script>

    <?php
    $message = flash('msg');
    if (!empty($message)) {
    ?>

    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?=$message ?>!',
    })

    <?php
    } ?>
</script>
</body>
</html>
