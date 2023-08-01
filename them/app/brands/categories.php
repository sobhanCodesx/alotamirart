<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo limit_words(trim($item['des'], " "), 160) ?>">
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <title><?= $item['name'] ?> - <?=$dataSeo['title']?></title>
</head>
<body>
<?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>
<div class="container mt-3">
    <div class="aling-item-center mt-3">
        <div class="container">
            <p class="text-center"><img class="d-block m-auto img-res-brand" src="<?= assets($item['img']) ?>"
                                        alt="<?= $item['name'] ?> - <?=$dataSeo['title']?>"></p>
        </div>
    </div>

    <h5 style="text-align: justify" class="alert alert-secondary mt-3 text-info-brand"><?= $item['des'] ?></h5>
</div>
<section>
    <div class="bge mt-5">
        <div class="container bge p-5">
            <h4 class="text-center text-white"> نمایندگی آخرین مقالات</h4>
            <div class="row">
                <?php foreach ($brands as $b) { ?>
                    <div class="col-12 col-md-5 col-lg-3">
                        <div class="parent">
                            <a href="<?= assets($b['slug'] . "/" . $b['id']) ?>">
                                <div class="img">
                                    <img src="<?= assets($b['img']) ?>" width="100%" alt=""/>
                                </div>
                            </a>
                            <div class="caption-item">
                                <div class="title">
                                    <a class="" href="<?= assets($b['slug'] . "/" . $b['id']) ?>"><?= $b['title'] ?></a>
                                </div>
                                <div class="caption">
                                    <p>
                                        <?php echo limit_words(trim($b['content'], " "), 30) ?>
                                    </p>
                                </div>
                                <div class="like-item d-linline">
                                    <a class="" href="<?= assets($b['slug'] . "/" . $b['id']) ?>">
                                        <button class="btn btn-outline-success form-control">
                                            ادامه
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="container mt-5 mb-3">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php echo get_pag_cat($page, 'brands/categories/',$item['id'],$pages, ''); ?>
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
    </div>
</section>

<?php require_once BASE_PATH . "/them/app/layout/footer.php" ?>
<?php require_once BASE_PATH . "/them/app/layout/js.php" ?>
</body>
</html>