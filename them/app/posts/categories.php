<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <title><?= $item['title'] ?> - <?=$dataSeo['title']?></title>
</head>
<body>
<?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>
<div class="container">
    <div class="m-auto">
        <h5 style="text-align: justify" class="alert alert-success text-center fs-1 mt-3"><?= $item['title'] ?> -
            صفحه <?= $page; ?></h5>

    </div>
</div>
<div class="bge mt-5">
    <div class="container bge p-5">
        <h4 class="text-white text-center"> <?= $item['title'] ?> آخرین مقالات</h4>
        <div class="row mt-2">
            <?php foreach ($post as $b) { ?>
                <div class="col-12 col-md-5 col-lg-3">
                    <div class="parent">
                        <a href="<?= assets('post/' . $b['id']) ?>">
                            <div class="img">
                                <img src="<?= assets($b['img']) ?>" width="100%" alt=""/>
                            </div>
                        </a>
                        <div class="caption-item">
                            <div class="title">
                                <a class="" href="<?= assets('post/' . $b['id']) ?>"><?= $b['title'] ?></a>
                            </div>
                            <div class="caption">
                                <p>
                                    <?php echo limit_words(trim($b['content'], " "), 30) ?>
                                </p>
                            </div>
                            <div class="like-item d-linline">
                                <a class="" href="<?= assets('post/' . $b['id']) ?>">
                                    <button class="btn btn-outline-success form-control">
                                        ادامه
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="container mt-5 mb-3">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <?php echo get_pag_cat($page, 'posts/categories/', $item['id'], $pages, ''); ?>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<div class="mt-4">
    <?php require_once BASE_PATH . "/them/app/layout/footer.php" ?>
</div>
<?php require_once BASE_PATH . "/them/app/layout/js.php" ?>
</body>
</html>