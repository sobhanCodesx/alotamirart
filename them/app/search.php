<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?=assets('public/src/img/logo.png') ?>">
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <title><?= $result ?> - لباسشویی تعمیر</title>
</head>
<body>
<?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-12 col-md-12 mt-5">
            <div class="">
                <h5 style="text-align: justify" class="alert alert-success text-center fs-1 mt-3"><?= $result ?></h5>
            </div>
        </div>
    </div>
</div>
<div class="bge mt-5">
    <div class="container bge p-5">
        <h4 class="text-center"> <?= $result ?></h4>
        <div class="row">
            <?php foreach ($post as $b) { ?>
                <div class="col-12 col-md-5 col-lg-3">
                    <div class="parent">
                        <a href="<?=assets('post/brand/'.$b['id']) ?>">
                            <div class="img">
                                <img src="<?= assets($b['img']) ?>" width="100%" alt=""/>
                            </div>
                        </a>
                        <div class="caption-item">
                            <div class="title">
                                <a class="" href="<?=assets('post/brand/'.$b['id']) ?>"><?= $b['title'] ?></a>
                            </div>
                            <div class="caption">
                                <p>
                                    <?php echo limit_words(trim($b['content'], " "), 30) ?>
                                </p>
                            </div>
                            <div class="like-item d-linline">
                            <a class="" href="<?=assets('post/brand/'.$b['id']) ?>"><button class="btn btn-outline-success form-control">
                                    ادامه
                                </button></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . "/them/app/layout/footer.php" ?>
<?php require_once BASE_PATH . "/them/app/layout/js.php" ?>
</body>
</html>