<!-- start header   -->
<div style="background:<?=$dataSeo['bg_topheader']?>" class="top-menu">
    <div class="container">
        <div class="topbar-right">
            <ul>
                <li>
                    <a class="text-white" href="tel:09---513698"
                    ><i class="fas fa-phone"></i
                        ></a>
                    <span> مشاوره و پشتیبانی واتساپ : <?=$dataFooter['phon'] ?></span>
                </li>
                <li>
                    <i class="fa fa-envelope"></i>
                    <span><?=$dataFooter['email'] ?></span>
                </li>
            </ul>
        </div>

        <div class="topbar-left">
            <ul>
                <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"> -->
                <li>
                    <i
                            class="fas fa-search"
                            data-bs-toggle="modal"
                            data-bs-target="#exampleModal"
                    ></i>
                </li>
                <!-- </button> -->
                <!-- Modal -->
                <div
                        class="modal fade"
                        id="exampleModal"
                        tabindex="-1"
                        aria-labelledby="exampleModalLabel"
                        aria-hidden="true"
                >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Modal title
                                </h5>
                                <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                ></button>
                            </div>
                            <div class="modal-body">

                                <form method="post" action="<?= assets('search/1') ?>">
                                    <input style="border-radius: 20px;box-shadow: 0 0 10px 0 #000" name="search"
                                           type="search" class="form-control bg-light search-modal"
                                           placeholder="نام شهر و برند سرچ را کنید">
                                    <button style="margin-top: -20px" class="search-btn btn-success"><i
                                                class="fas fa-search text-white"></i>
                                    </button>
                                    <
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </ul>
        </div>
    </div>
</div>
<header class="header">
    <div class="container">
        <div class="logo">
            <a href="<?=assets('/')?>"><img src="<?=assets($dataSeo['logo']) ?>"  alt="<?=$dataSeo['title']?>"/></a>
        </div>

        <div id="hamberger">
            <i class="fas fa-bars"></i>
        </div>
        <nav class="navigation">
            <ul>
                <li><a href="<?= assets('/') ?>">صفحه اصلی</a></li>
                <?php foreach ($menu as $m) { ?>
                    <li><a href="<?= assets('posts/categories/' . $m['id'] . "/1") ?>"><?= $m['title'] ?></a></li>
                <?php } ?>
                <div class="container">
                    <div class="sign">
                        <?php if (isset($_SESSION['name'])) { ?>
                            <nav>
                                <div class="btn-group dropdown">
                                    <button type="button" style="margin-left: 60px !important;"  class="btn btn-success text-white dropdown-toggle navbar"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <?= $_SESSION['name']; ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php if ($_SESSION['role'] == 1) { ?>
                                            <li><a class="dropdown-item" href="<?= assets('admin/dashboard') ?>">پنل
                                                    ادمین</a></li>
                                        <?php } ?>
                                        <?php if ($_SESSION['w'] == 1) { ?>
                                            <li><a class="dropdown-item" href="<?= assets('panelcp/') ?>">پنل کاربری</a>
                                </li>
                                <li><a class="dropdown-item" href="<?= assets('profile/'.getByUser('user_name').'/'.getByUser('id')) ?>">پروفایل</a>
                                            <li><a class="dropdown-item" href="<?= assets('user/posts/create') ?>">نوشتن
                                                    مقاله</a></li>
                                            <li><a class="dropdown-item" href="<?= assets('user/post/1') ?>">نمایش
                                                    مقالات</a></li>
                                            <li><a class="dropdown-item" href="<?= assets('userbrand/create') ?>">نوشتن
                                                    تبلیغات برند ها</a></li>
                                            <li><a class="dropdown-item" href="<?= assets('user/brand/1') ?>">مقاله های
                                                    برند ها</a></li>
                                        <?php } ?>
                                        <li><a class="dropdown-item" href="#"><?= $_SESSION['user_name'] ?></a></li>
                                        <li><a class="dropdown-item" href="<?= assets('logout') ?>">خروج</a></li>
                                    </ul>
                                </div>
                            </nav>
                        <?php } else { ?>
                            <a class="mt-2" href="<?= assets('register'); ?>"> ثبت نام </a>
                            <a class="mt-2" href="<?= assets('login'); ?>"> ورود </a>
                        <?php } ?>
                    </div>

                </div>
            </ul>
        </nav>
        <span class="signmobile">
          <a> <i class="fas fa-user-lock"></i> </a
          ></span>
    </div>

    <nav class="main-menu">
        <ul>
            <li><a href="<?= assets('/') ?>">صفحه اصلی</a></li>
            <?php foreach ($menu as $m) { ?>
                <li><a href="<?= assets('posts/categories/' . $m['id'] . "/1") ?>"><?= $m['title'] ?></a></li>
            <?php } ?>
        </ul>
    </nav>
    <div class="container">
        <div class="sign">
            <?php if (isset($_SESSION['name'])) { ?>
                <nav>
                    <div style="margin-left: 4rem;" class="btn-group dropdown">
                        <button type="button" class="btn btn-success text-white dropdown-toggle navbar"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <?= $_SESSION['name']; ?>
                        </button>
                        <ul class="dropdown-menu">
                            <?php if ($_SESSION['role'] == 1) { ?>
                                <li><a class="dropdown-item" href="<?= assets('admin/dashboard') ?>">پنل ادمین</a></li>
                            <?php } ?>
                            <?php if ($_SESSION['w'] == 1) { ?>
                                <li><a class="dropdown-item" href="<?= assets('panelcp/') ?>">پنل کاربری</a>
                                </li>
                                <li><a class="dropdown-item" href="<?= assets('profile/'.getByUser('user_name').'/'.getByUser('id')) ?>">پروفایل</a>
                                </li>
                                </li>
                                <li><a class="dropdown-item" href="<?= assets('user/posts/create') ?>">نوشتن مقاله</a>
                                </li>
                                <li><a class="dropdown-item" href="<?= assets('user/post/1') ?>">نمایش مقالات</a></li>
                                <li><a class="dropdown-item" href="<?= assets('userbrand/create') ?>">نوشتن تبلیغات برند
                                        ها</a></li>
                                <li><a class="dropdown-item" href="<?= assets('user/brand/1') ?>">مقاله های برند ها</a>
                                </li>
                            <?php } ?>
                            <li><a class="dropdown-item" href="#"><?= $_SESSION['user_name'] ?></a></li>
                            <li><a class="dropdown-item" href="<?= assets('logout') ?>">خروج</a></li>
                        </ul>
                    </div>
                </nav>
            <?php } else { ?>
                <a href="<?= assets('register'); ?>"> ثبت نام </a>
                <a href="<?= assets('login'); ?>"> ورود </a>
            <?php } ?>
        </div>

    </div>
</header>
<!-- // end header  -->
