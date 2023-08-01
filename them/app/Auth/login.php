<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <link rel="stylesheet" href="<?= assets('public/src/css/auth.css') ?>">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600' rel='stylesheet'
          type='text/css'>
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
    <title>ورود</title>
</head>
<body>
<?php require_once BASE_PATH . "/them/app/layout/header.php"; ?>

<div class="container mt-3">
    <div class="testbox">
        <h1>ورود</h1>
        <form action="<?= assets('logined') ?>" method="post">
            <label id="icon" class="box-icon" for="name"><i class="fa fa-address-card icon-reg"></i></label>
            <input type="text" class="box-icon p-2" name="user_name" id="name" placeholder="نام کاربری" required/>
            <label id="icon" for="name"><i class="icon-shield icon-reg"></i></label>
            <input type="password" class="p-2" name="password" id="name" placeholder="پسورد" required/>
            <input type="submit" value="ارسال"  class="btn btn-success">
        </form>
    </div>
</div>
<?php require_once BASE_PATH . "/them/app/layout/footer.php" ?>
<?php require_once BASE_PATH . "/them/app/layout/js.php" ?>
<script>

    <?php
    $message = flash('login_error');
    if (!empty($message)) {
    ?>
    Swal.fire({
        icon: 'error',
        title: 'خطا',
        text: '<?= $message ?>',
        footer: '<a style="nav-link" href="<?= assets('/register') ?>">ثبت نام</a>'
    })
    <?php }   ?>

    <?php
    $message = flash('saveuser');
    if (!empty($message)) {
    ?>
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
    <?php }   ?>

</script>
</body>
</html>
