<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- ====== SEO Meta Tags ====== -->
    <title><?= $dataSeo['title'] ?></title>
    <meta name="description" content="<?= $dataSeo['description'] ?>" />
    <meta name="keywords" content="<?= $dataSeo['keyword'] ?>" />
    <link rel="canonical" href="https://damavandservice.com/" />

    <!-- ====== Open Graph ====== -->
    <meta property="og:type" content="company" />
    <meta property="og:title" content="<?= $dataSeo['title'] ?>" />
    <meta property="og:description" content="<?= $dataSeo['description'] ?>" />
    <meta property="og:image" content="<?= assets($dataSeo['logo']) ?>" />
    <meta property="og:site_name" content="<?= $dataSeo['title'] ?>" />
    <meta property="og:url" content="https://damavandservice.com/" />

    <!-- ====== Font Awesome ====== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>

    <style>
        /* ====== استایل‌های صفحه‌بندی ====== */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
            flex-wrap: wrap;
            direction: ltr;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            background: #fff;
            border: 2px solid #e9edf2;
            border-radius: 8px;
            color: #0f172a;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .pagination a:hover {
            border-color: #facc15;
            background: #fef9e7;
        }
        .pagination a.active {
            background: #facc15;
            border-color: #facc15;
            color: #0f172a;
        }
        .pagination a.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        
        /* ====== استایل‌های قبلی ====== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.8;
            overflow-x: hidden;
        }
        a {
            text-decoration: none;
            color: #2563eb;
            transition: 0.3s;
        }
        a:hover {
            color: #1d4ed8;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ====== BANNER ====== */
        .baner-header {
            min-height: 100vh;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        .baner-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: -1;
        }
        .cover-title {
            text-align: center;
            padding: 40px 20px;
        }
        .cover-title h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #facc15;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 16px;
            opacity: 0;
            transform: translateY(-30px);
            animation: fadeDown 0.8s ease forwards;
        }
        .cover-title h2 {
            font-size: 1.8rem;
            font-weight: 300;
            color: #ffffff;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
            margin-bottom: 32px;
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeDown 0.8s ease 0.3s forwards;
        }
        @keyframes fadeDown {
            to { opacity: 1; transform: translateY(0); }
        }

        .search-container {
            display: flex;
            max-width: 560px;
            margin: 0 auto;
            background: white;
            border-radius: 60px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        .search-input {
            flex: 1;
            padding: 16px 24px;
            border: none;
            font-size: 1rem;
            outline: none;
            background: transparent;
            color: #1e293b;
        }
        .search-input::placeholder {
            color: #94a3b8;
        }
        .search-btn {
            padding: 16px 28px;
            background: #facc15;
            border: none;
            color: #0f172a;
            font-size: 1.2rem;
            cursor: pointer;
            transition: 0.3s;
        }
        .search-btn:hover {
            background: #eab308;
        }

        /* ====== بخش مقالات ====== */
        .section-title {
            font-size: 2.2rem;
            font-weight: 700;
            text-align: center;
            color: #ffffff;
            margin-bottom: 40px;
            letter-spacing: -0.5px;
        }
        .section-title-dark {
            color: #0f172a;
        }
        .bge {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            padding: 60px 0;
        }
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 28px;
        }
        .post-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: 0.4s ease;
            opacity: 0;
            transform: translateY(30px);
        }
        .post-card.animated {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .post-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.12);
        }
        .post-card .img {
            overflow: hidden;
            height: 200px;
        }
        .post-card .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s ease;
        }
        .post-card:hover .img img {
            transform: scale(1.05);
        }
        .post-card .caption-item {
            padding: 20px 22px 24px;
        }
        .post-card .caption-item .title a {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            display: block;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .post-card .caption-item .title a:hover {
            color: #2563eb;
        }
        .post-card .caption-item .caption p {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .post-card .caption-item .btn {
            width: 100%;
            padding: 10px;
            border-radius: 40px;
            font-weight: 600;
            border: 2px solid #2563eb;
            background: transparent;
            color: #2563eb;
            transition: 0.3s;
            cursor: pointer;
        }
        .post-card .caption-item .btn:hover {
            background: #2563eb;
            color: white;
        }

        /* ====== سایر بخش‌ها ====== */
        .bge2 {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: 60px 0;
        }
        .info-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }
        .img-info-grapi {
            min-height: 380px;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .img-info-grapi::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .img-info-grapi .cover-box {
            position: relative;
            z-index: 2;
            padding: 20px;
        }
        .img-info-grapi .cover-box h3 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #facc15;
            margin-bottom: 12px;
        }
        .img-info-grapi .cover-box h4 {
            font-size: 1.2rem;
            font-weight: 300;
            color: #ffffff;
        }

        #brand {
            padding: 60px 0;
        }
        #brand h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 32px;
        }
        .brand-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 24px;
        }
        .brand-item {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid #e9edf2;
            transition: 0.3s;
        }
        .brand-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
        }
        .brand-item img {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }
        .brand-item .brand-info {
            padding: 16px 18px 20px;
        }
        .brand-item .brand-info .brand-name {
            font-weight: 700;
            font-size: 1rem;
            color: #0f172a;
            display: block;
            margin-bottom: 8px;
        }
        .brand-item .brand-info p {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .brand-item .brand-info .btn {
            width: 100%;
            padding: 8px;
            border-radius: 40px;
            font-weight: 600;
            border: 2px solid #0f172a;
            background: transparent;
            color: #0f172a;
            transition: 0.3s;
            cursor: pointer;
        }
        .brand-item .brand-info .btn:hover {
            background: #0f172a;
            color: white;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr 1fr;
            gap: 30px;
            margin-top: 40px;
        }
        .info-box {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            padding: 32px 24px;
            border-radius: 28px;
            color: white;
        }
        .info-box .info-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }
        .info-box .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .info-box .info-item svg {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            fill: #facc15;
        }
        .info-box .info-item a {
            color: #facc15;
        }
        .info-box .info-item a:hover {
            color: #ffffff;
        }

        .form-box {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: 32px 28px;
            border-radius: 28px;
            color: white;
        }
        .form-box .form-title {
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 24px;
        }
        .form-box input, .form-box textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 1rem;
            transition: 0.3s;
            font-family: inherit;
        }
        .form-box input::placeholder, .form-box textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        .form-box input:focus, .form-box textarea:focus {
            outline: none;
            border-color: #facc15;
            background: rgba(255, 255, 255, 0.08);
        }
        .form-box textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-box .btn-submit {
            width: 100%;
            padding: 14px;
            border-radius: 40px;
            border: 2px solid #facc15;
            background: transparent;
            color: #facc15;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 12px;
        }
        .form-box .btn-submit:hover {
            background: #facc15;
            color: #0f172a;
        }

        @media (max-width: 992px) {
            .info-grid { grid-template-columns: 1fr 1fr; }
            .info-grid .form-box { grid-column: 1 / -1; }
            .info-row { grid-template-columns: 1fr 1fr; }
            .info-row .img-info-grapi:last-child { grid-column: 1 / -1; }
        }
        @media (max-width: 768px) {
            .cover-title h1 { font-size: 2.2rem; }
            .cover-title h2 { font-size: 1.2rem; }
            .posts-grid { grid-template-columns: 1fr 1fr; }
            .info-grid { grid-template-columns: 1fr; }
            .info-row { grid-template-columns: 1fr; }
            .brand-grid { grid-template-columns: 1fr 1fr; }
            .search-container { max-width: 100%; border-radius: 30px; }
            .search-input { padding: 12px 18px; font-size: 0.9rem; }
            .search-btn { padding: 12px 20px; }
        }
        @media (max-width: 480px) {
            .posts-grid { grid-template-columns: 1fr; }
            .brand-grid { grid-template-columns: 1fr; }
            .cover-title h1 { font-size: 1.8rem; }
            .section-title { font-size: 1.6rem; }
        }

        .animate-item {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .animate-item.animated {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>

    <!-- ====== BANNER ====== -->
    <section class="baner-header" style="background-image: url('<?= $dataSeo['header'] ?>')">
        <?php
        $message = flash('login');
        if (!empty($message)) {
            echo "<script> Swal.fire('Good luck!', '{$message}', 'success'); </script>";
        }
        ?>
        <div class="container cover-title">
            <h1><?= $dataSeo['title_h1'] ?></h1>
            <h2><?= $dataSeo['title_h2'] ?></h2>
            <div class="body">
                <form method="post" action="<?= assets('search/1') ?>">
                    <div class="search-container">
                        <input type="text" name="search" placeholder="جستجو..." class="search-input" />
                        <button class="search-btn" name="send"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- ====== شهرهای تحت پوشش ====== -->
    <section id="cities-covered" style="padding: 60px 0; background: #f8fafc;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <a href="<?= assets('cities') ?>" style="display: block; background: linear-gradient(135deg, #ffffff, #f8fafc); border-radius: 30px; padding: 40px 30px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.06); border: 2px solid #e9edf2; transition: all 0.4s ease; text-decoration: none; user-select: none;" onmouseover="this.style.boxShadow='0 20px 60px rgba(0,0,0,0.12)'; this.style.transform='translateY(-3px)';" onmouseout="this.style.boxShadow='0 10px 40px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)';">
                <div style="display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <span style="font-size: 2.5rem;">🏙️</span>
                    <h2 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -1px;">شهرهای تحت پوشش</h2>
                    <span style="font-size: 1.5rem; color: #0f172a; display: inline-block; background: #f1f5f9; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">➡️</span>
                </div>
                <div style="width: 100px; height: 4px; background: linear-gradient(90deg, #facc15, #0f172a); margin: 15px auto 0; border-radius: 2px;"></div>
                <p style="color: #94a3b8; font-size: 0.9rem; margin: 12px 0 0; font-weight: 500;">مشاهده همه شهرها</p>
            </a>
        </div>
    </section>

    <!-- ====== آخرین مقالات ====== -->
    <section class="bge">
        <div class="container">
            <h4 class="section-title">آخرین مقالات</h4>
            <div class="posts-grid">
                <?php foreach ($post as $b): ?>
                    <div class="post-card animate-item">
                        <a href="<?= assets('post/' . $b['id']) ?>">
                            <div class="img">
                                <img src="<?= assets($b['img']) ?>" alt="<?= $b['title'] ?>" />
                            </div>
                        </a>
                        <div class="caption-item">
                            <div class="title">
                                <a href="<?= assets('post/' . $b['id']) ?>"><?= $b['title'] ?></a>
                            </div>
                            <div class="caption">
                                <p><?= limit_words(trim($b['content'], " "), 30) ?></p>
                            </div>
                            <a href="<?= assets('post/' . $b['id']) ?>">
                                <button class="btn">ادامه مطلب</button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ====== ✅ صفحه‌بندی ====== -->
            <?php if (isset($total_pages) && $total_pages > 1): ?>
            <div class="pagination">
                <a href="/?page=1" class="<?= $current_page == 1 ? 'disabled' : '' ?>">❮❮</a>
                <a href="/?page=<?= $current_page - 1 ?>" class="<?= $current_page <= 1 ? 'disabled' : '' ?>">❮ قبلی</a>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="/?page=<?= $i ?>" class="<?= $i == $current_page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <a href="/?page=<?= $current_page + 1 ?>" class="<?= $current_page >= $total_pages ? 'disabled' : '' ?>">بعدی ❯</a>
                <a href="/?page=<?= $total_pages ?>" class="<?= $current_page == $total_pages ? 'disabled' : '' ?>">❯❮</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ====== سایر بخش‌ها (همون قبلی) ====== -->
    <!-- ====== INFO ROW ====== -->
    <section class="info-row">
        <div class="img-info-grapi" style="background-image: url('<?= $dataHeader['img_one'] ?>')">
            <div class="cover-box">
                <h3><?= $dataHeader['title_one'] ?></h3>
                <h4><?= $dataHeader['description_one'] ?></h4>
            </div>
        </div>
        <div class="img-info-grapi" style="background-image: url('<?= $dataHeader['img_two'] ?>')">
            <div class="cover-box">
                <h3><?= $dataHeader['title_two'] ?></h3>
                <h4><?= $dataHeader['description_two'] ?></h4>
            </div>
        </div>
        <div class="img-info-grapi" style="background-image: url('<?= $dataHeader['img_tree'] ?>')">
            <div class="cover-box">
                <h3><?= $dataHeader['title_tree'] ?></h3>
                <h4><?= $dataHeader['description_tree'] ?></h4>
            </div>
        </div>
    </section>

    <!-- ====== نمایندگی آخرین مقالات ====== -->
    <section class="bge2">
        <div class="container">
            <h4 class="section-title">نمایندگی آخرین مقالات</h4>
            <div class="posts-grid">
                <?php foreach ($brands as $b): ?>
                    <div class="post-card animate-item">
                        <a href="<?= assets($b['slug'] . '/' . $b['id']) ?>">
                            <div class="img">
                                <img src="<?= assets($b['img']) ?>" alt="<?= $b['title'] ?>" />
                            </div>
                        </a>
                        <div class="caption-item">
                            <div class="title">
                                <a href="<?= assets($b['slug'] . '/' . $b['id']) ?>"><?= $b['title'] ?></a>
                            </div>
                            <div class="caption">
                                <p><?= trim(limit_words(trim($b['content'], " "), 30), " ") ?></p>
                            </div>
                            <a href="<?= assets($b['slug'] . '/' . $b['id']) ?>">
                                <button class="btn">ادامه مطلب</button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ====== برندها ====== -->
    <section id="brand">
        <div class="container">
            <h3 class="text-center">پوشش نمایندگی‌ها</h3>
            <div class="brand-grid">
                <?php foreach ($brand as $b): ?>
                    <div class="brand-item">
                        <img src="<?= assets($b['img']) ?>" alt="<?= $b['name'] ?>" />
                        <div class="brand-info">
                            <span class="brand-name"><?= $b['name'] ?></span>
                            <p><?= trim(limit_words(trim($b['des'], " "), 30), " ") ?>...</p>
                            <a href="<?= assets('brands/categories/' . $b['id'] . '/1') ?>">
                                <button class="btn">ادامه</button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ====== اطلاعات و تماس ====== -->
    <div class="container">
        <div class="info-grid">
            <div class="info-box">
                <div class="info-title">جزئیات تماس</div>
                <div class="info-item">
                    <svg viewBox="0 0 16 16"><path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h8zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z"/><path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                    <span>مدیریت: دانش اسدی</span>
                </div>
                <div class="info-item">
                    <svg viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/></svg>
                    <span>شماره تماس: <a href="tel:<?= $dataFooter['phon'] ?>"><?= $dataFooter['phon'] ?></a></span>
                </div>
                <div class="info-item">
                    <svg viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>
                    <span>ساعات کاری: شنبه - جمعه</span>
                </div>
            </div>
            <div class="form-box">
                <div class="form-title">درخواست مشاوره آنلاین</div>
                <form action="" method="post">
                    <input type="text" class="form-control" placeholder="نام و نام خانوادگی" />
                    <input type="text" class="form-control" placeholder="شماره تماس" style="margin-top: 14px;" />
                    <input type="text" class="form-control" placeholder="آدرس" style="margin-top: 14px;" />
                    <label style="display: block; margin-top: 18px; color: rgba(255,255,255,0.7);">مشکل لوازم خانگی</label>
                    <textarea style="margin-top: 8px;"></textarea>
                    <button type="submit" class="btn-submit">ارسال</button>
                </form>
            </div>
            <div class="info-box">
                <div class="info-title">مشاوره</div>
                <p style="color: rgba(255,255,255,0.85); line-height: 2; font-size: 1rem;"><?= $dataSeo['text_about'] ?></p>
            </div>
        </div>
    </div>

    <!-- ====== FOOTER ====== -->
    <div class="mt-4">
        <?php require_once BASE_PATH . '/them/app/layout/footer.php'; ?>
    </div>
    <?php require_once BASE_PATH . '/them/app/layout/js.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
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
            liveTyping($(".title-h1"), "<?= $dataSeo['title_h1'] ?>", 50, 500);

            const animateItems = document.querySelectorAll('.animate-item');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -20px 0px' });
            animateItems.forEach(item => {
                observer.observe(item);
            });
        });
    </script>

</body>
</html>