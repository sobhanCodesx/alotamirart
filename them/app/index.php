<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8"/>
    <meta property="og:type" content="company">
    <meta name="canonical" content="https://damavandservice.com/">
    <meta property="og:title" content="دماوند سرویس">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.12/typed.min.js"></script>
    <meta property="og:description" content="<?=$dataSeo['description']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta property="og:image" content="<?=assets($dataSeo['logo']) ?>">
    <meta property="og:site_name" content="<?=$dataSeo['title']?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description"
          content="<?=$dataSeo['description']?>">
    <meta name="keyword" content="<?=$dataSeo['keyword']?>">
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"
    />

    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

    <title><?=$dataSeo['title']?></title>
 
    <style>
    .title-h1, .title-h2 {
        opacity: 0;
        transform: translateY(-20px);
    }

    .title-h1.show, .title-h2.show {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 1s ease, transform 1s ease;
    }
    <style>
    /* سبک‌های انیمیشن */
    .animate-item {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.8s, transform 0.8s;
    }
    .animate-item.animated {
        opacity: 1;
        transform: translateY(0);
    }
    /* سایر سبک‌ها و تغییرات ظاهری دلخواه خود را نیز اینجا اضافه کنید */
</style>
    </style>
</head>
<body>
<?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>
<section class="">
    <div class="baner-header" style="background-image: url('<?= $dataSeo["header"]?>')" >
        <?php
        $message = flash('login');
        if (!empty($message)) {
            ?>
            <script>
                Swal.fire(
                    'Good luck!',
                    '<?=$message ?>',
                    'success'
                )
            </script>

            <?php
        } ?>
        <div class="container cover-title">
            <h1 class="text-warning text-center title-h1"><?=$dataSeo['title_h1']?></h1>
            <h2 class="text-white text-center title-h2"><?=$dataSeo['title_h2']?></h2>
            <div class="body">
                <form method="post" action="<?= assets('search/1') ?>">
                    <div class="search-container">
                        <input type="text" name="search" placeholder="جستجو"
                               class="search-input text-dark">
                        <button class="search-btn" name="send"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <br>

        </div>
    </div>
</section>
<!-- // slislider -->

<div>
    <section>
        <div class="bge">
            <div class="container bge p-5">
                <h4 class="text-center fs-1 text-white"> آخرین مقالات</h4>
                <div class="row">
                    <?php foreach ($post as $b) { ?>
                        <div class="col-12 col-md-5 col-lg-3 animate-item">
                            <div class="parent">
                                <a href="<?= assets('post/' . $b['id']) ?>">
                                    <div class="img">
                                        <img src="<?= assets($b['img']) ?>" width="100%" alt="<?= $b['title'] ?>"/>
                                    </div>
                                </a>
                                <div class="caption-item">
                                    <div class="title">
                                        <a class="<?= assets('post/' . $b['id']) ?>"
                                           href="<?= assets('post/' . $b['id']) ?>"><?= $b['title'] ?></a>
                                    </div>
                                    <div class="caption">
                                        <p>
                                            <?php echo limit_words(trim($b['content'], " "), 30) ?>
                                        </p>
                                    </div>
                                    <div class="like-item d-linline">
                                        <a class="<?= assets('post/' . $b['id']) ?>"
                                           href="<?= assets('post/' . $b['id']) ?>">
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
            </div>

        </div>
    </section>
</div>

<section class="container">
    <div class="container">

</section>


<!-- ////// end box citys -->
<section class="">
    <div id="img1" style="background-image:url('<?=$dataHeader["img_one"] ?>')" class="col-12 col-md-4 img-info-grapi">
        <div class="text-info-img">
            <div class="cover-box">
                <h3 class="text-center fs-1 text-warning text-danger"><p class="d-inline fs-1 text-danger text-white size"><?=$dataHeader['title_one'] ?></p></h3>
                    <h4 class="text-center fs-3 mt-4 text-white"><?=$dataHeader['description_one'] ?></h4>
            </div>
        </div>
    </div>
    <div id="img2" style="background-image:url('<?=$dataHeader["img_two"] ?>')" class="col-12 col-md-4 img-info-grapi">
        <div class="text-info-img">
            <div class="cover-box">
                <h3 class="text-center fs-1 text-danger"><?=$dataHeader['title_two'] ?></h3>
                    <h4 class="text-center fs-3 mt-4 text-white"><?=$dataHeader['description_two'] ?></h4>
            </div>
        </div>
    </div>
    <div id="img3" style="background-image:url('<?=$dataHeader["img_tree"] ?>')" class="col-12 col-md-4 img-info-grapi">
        <div class="text-info-img">
            <div class="cover-box">
                <h3 class="text-center fs-1 text-danger"><?=$dataHeader['title_tree'] ?></h3>
                    <h4 class="text-center fs-3 mt-4 text-white"><?=$dataHeader['description_tree'] ?></h4>
            </div>
        </div>
    </div>
</section>
<!-- // end slislider -->
<section>
    <div class="bge2">
        <div class="container bge2 p-5">
            <h4 class="text-center text-white fs-1"> نمایندگی آخرین مقالات</h4>
            <div class="row">
                <?php foreach ($brands as $b) { ?>
                    <div class="col-12 col-md-5 col-lg-3">
                        <div class="parent">
                            <a href="<?= assets($b['slug']."/" . $b['id']) ?>">
                                <div class="img">
                                    <img src="<?= assets($b['img']) ?>" width="100%" alt=""/>
                                </div>
                            </a>
                            <div class="caption-item">
                                <div class="title">
                                    <a class="" href="<?= assets($b['slug']."/" . $b['id']) ?>"><?= $b['title'] ?></a>
                                </div>
                                <div class="caption">
                                    <p>
                                        <?php echo trim(limit_words(trim($b['content'], " "), 30)," ") ?>
                                    </p>
                                </div>
                                <div class="like-item d-linline">
                                    <a class="" href="<?= assets($b['slug']."/" . $b['id']) ?>">
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
        </div>

    </div>
</section>
<!-- /////// start posts -->
<!-- /////// end posts -->
<section>
    <div id="brand" class="container">
        <h3 class="text-center mt-2 mb-2">پوشش نمایندگی ها</h3>
        <div class="row">
            <?php foreach ($brand as $b) { ?>
                <div class="col-12 col-md-3 mt-1">
                    <div>
                        <img style="width: 100%;height: 160px" src="<?= assets($b['img']) ?>" alt="<?= $b['name'] ?>">
                        <button class="accordion text-white p-2 btn btn-info"><?= $b['name'] ?></button>
                        <div id="panel" class="panel bg-light">
                            <div class="">
                                <div class="mt-3">
                                    <p style="text-align: justify"><?php echo trim(limit_words(trim($b['des'], " "), 30)," ") ?>
                                        ...</p>
                                    <a href="<?= assets('brands/categories/' . $b['id'] . "/1") ?>">
                                        <button class="btn btn-outline-secondary form-control">ادامه</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<!--//// post brands -->
<div class="container mt-5">
   <div class="container">
   <div class="row">
        <h3 class="text-center fs-3">اطلاعات</h3>
        <div class="col-12 col-md-4 mt-5">
            <div class="bg-info-box p-4 mt-5">
                <p class="fs-4 text-white text-center mt-2">جزئیات تماس</p>
                <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="25" fill="currentColor" class="text-white bi bi-file-person" viewBox="0 0 16 16">
                            <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z" />
                            <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        </svg>
                        <p class="d-inline text-white">مدیریت : امیر حسین باجلان  </p>
                    </span>
                <span class="mt-3 d-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="25" fill="currentColor" class="text-white bi bi-telephone-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                        </svg>
                        <p class="d-inline text-white">شماره تماس : </p> <a class="text-white" href="tel:<?=$dataFooter['phon']?>"><?=$dataFooter['phon']?></a> <br> <a class="text-white" style="margin-right: 126px;" href="tel:<?=$dataFooter['phon']?>"><?=$dataFooter['phon']?></a>
                    </span>
                <span class="mt-3 d-block">
                        <svg  xmlns="http://www.w3.org/2000/svg" width="30" height="25" fill="currentColor" class="text-white bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                        </svg>
                        <p class="d-inline text-white">ساعات کاری <?=$dataSeo['title']?> :</p>
                        <p class="d-inline text-white">شنبه - جمعه</p>
                    </span>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="bg-info-form">
                <div class="container mt-4 p-3">
                    <p class="text-center text-white fs-3">در خواست مشاوره انلاین</p>
                    <form action="" method="post">
                        <input type="text" class="form-control" placeholder="نام و نام خانوادگی">
                        <input type="text" class="form-control mt-3" placeholder="شماره تماس">
                        <input type="text" class="form-control mt-3" placeholder="آدرس">
                        <label for="" class="mt-2 text-white">مشکل لوازم خانگی</label>
                        <textarea class="mt-3 form-control" style="height: 100px;" name="" id="" cols="20" rows="10">
                        </textarea>
                        <input type="submit" value="ارسال" class="btn btn-outline-success text-white mt-3 form-control" name="" id="">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mt-5">
            <div class="bg-info-box p-4 mt-md-5">
                <p class="fs-4 text-white text-center mt-2">مشاوره</p>
                <br>
                <p class="text-white fs-6 "><?=$dataSeo['text_about']?></p>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="mt-4">
    <?php require_once BASE_PATH . "/them/app/layout/footer.php" ?>
</div>
<?php require_once BASE_PATH . "/them/app/layout/js.php" ?>
<!-- لینک به کتابخانه jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- کد jQuery برای اعمال انیمیشن هنگام بارگذاری صفحه -->
<script>
  $(document).ready(function() {
    // اعمال انیمیشن به تگ h1
    $(".title-h1").addClass("show");

    // اعمال انیمیشن به تگ h2
    setInterval(() => {
        $(".title-h2").addClass("show");
    }, 1000);
  });
</script>
<!-- کد jQuery برای اعمال انیمیشن هنگام بارگذاری صفحه -->
<script>

  function liveTyping(element, text, speed, delay) {
    var i = 0;
    var interval = setInterval(function() {
      element.text(text.substring(0, i++));
      if (i > text.length) clearInterval(interval);
    }, speed);
    setTimeout(function() {
      element.addClass('show');
    }, delay);
  }

 
  $(document).ready(function() {
   
    liveTyping($(".title-h1"), "<?= $dataSeo['title_h1'] ?>", 50, 500);


  });
</script>
</script>

</body>
</html>
