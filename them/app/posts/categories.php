<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    
    <!-- ===== سئو ===== -->
    <title><?php echo isset($item['title']) ? strip_tags($item['title']) : ''; ?> - <?php echo isset($dataSeo['title']) ? strip_tags($dataSeo['title']) : 'وب‌سایت'; ?></title>
    <meta name="description" content="<?php echo isset($item['title']) ? 'لیست مقالات دسته‌بندی ' . strip_tags($item['title']) : ''; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- ===== Open Graph ===== -->
    <meta property="og:title" content="<?php echo isset($item['title']) ? strip_tags($item['title']) : ''; ?> - <?php echo isset($dataSeo['title']) ? strip_tags($dataSeo['title']) : ''; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:locale" content="fa_IR">
    
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
</head>
<body>
<?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>

<main>
    <!-- ===== هدر دسته‌بندی ===== -->
    <div class="container">
        <div class="m-auto">
            <h1 class="alert alert-success text-center fs-1 mt-3" style="text-align: justify; border-top: 4px solid #D4AF37; border-radius: 12px;">
                <?php echo isset($item['title']) ? strip_tags($item['title']) : ''; ?> - صفحه <?php echo isset($page) ? (int)$page : 1; ?>
            </h1>
        </div>
    </div>

    <!-- ===== بخش مقالات ===== -->
    <div class="bge mt-5">
        <div class="container bge p-3 p-md-5">
            <h2 class="text-white text-center mb-4" style="font-weight: 700; border-bottom: 3px solid #D4AF37; padding-bottom: 15px; display: inline-block; width: 100%;">
                <?php echo isset($item['title']) ? strip_tags($item['title']) : ''; ?> | آخرین مقالات
            </h2>
            
            <div class="row mt-4">
                <?php if (!empty($post) && is_array($post)): ?>
                    <?php foreach ($post as $b): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card h-100" style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; border-top: 3px solid #D4AF37; height: 100%; display: flex; flex-direction: column;">
                                
                                <a href="<?php echo assets('post/' . $b['id']); ?>" style="display: block; overflow: hidden;">
                                    <img src="<?php echo assets($b['img']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo isset($b['title']) ? strip_tags($b['title']) : ''; ?>"
                                         loading="lazy"
                                         style="width: 100%; height: 200px; object-fit: cover; transition: all 0.3s ease;"
                                         onmouseover="this.style.transform='scale(1.05)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                </a>
                                
                                <div class="card-body d-flex flex-column" style="padding: 18px; flex: 1;">
                                    <h3 class="card-title" style="font-size: 16px; font-weight: 700; margin-bottom: 10px; line-height: 1.5;">
                                        <a href="<?php echo assets('post/' . $b['id']); ?>" style="color: #1a3a5c; text-decoration: none; transition: color 0.3s;">
                                            <?php echo isset($b['title']) ? strip_tags($b['title']) : ''; ?>
                                        </a>
                                    </h3>
                                    
                                    <p class="card-text" style="font-size: 13px; color: #666; line-height: 1.8; flex: 1;">
                                        <?php echo isset($b['content']) ? limit_words(trim(strip_tags($b['content']), " "), 25) : ''; ?>
                                    </p>
                                    
                                    <a href="<?php echo assets('post/' . $b['id']); ?>" class="btn btn-outline-success w-100 mt-2" 
                                       style="border-color: #D4AF37; color: #1a3a5c; font-weight: 600; border-radius: 10px; transition: all 0.3s ease;"
                                       onmouseover="this.style.background='#D4AF37'; this.style.color='#0a0e27';"
                                       onmouseout="this.style.background='transparent'; this.style.color='#1a3a5c';">
                                        <i class="fas fa-arrow-left"></i> ادامه مطلب
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center p-4" style="background: rgba(212, 175, 55, 0.1); border: 2px solid #D4AF37; border-radius: 12px;">
                            <i class="fas fa-info-circle" style="color: #D4AF37; font-size: 24px; display: block; margin-bottom: 10px;"></i>
                            <p style="font-size: 18px; font-weight: 600; color: #1a3a5c;">هیچ مقاله‌ای در این دسته‌بندی یافت نشد!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- ===== پیجینیشن ===== -->
            <?php if (!empty($pages) && $pages > 1): ?>
            <div class="container mt-5 mb-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center" style="gap: 5px; flex-wrap: wrap;">
                        <li class="page-item <?php echo (isset($page) && $page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo assets('posts/categories/' . $item['id'] . '?page=' . ((isset($page) ? (int)$page : 1) - 1)); ?>" 
                               aria-label="Previous"
                               style="background: #ffffff; border: 2px solid #D4AF37; color: #1a3a5c; border-radius: 10px; margin: 0 3px; transition: all 0.3s ease;">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>

                        <?php echo get_pag_cat($page, 'posts/categories/', $item['id'], $pages, ''); ?>

                        <li class="page-item <?php echo (isset($page) && $page >= $pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo assets('posts/categories/' . $item['id'] . '?page=' . ((isset($page) ? (int)$page : 1) + 1)); ?>" 
                               aria-label="Next"
                               style="background: #ffffff; border: 2px solid #D4AF37; color: #1a3a5c; border-radius: 10px; margin: 0 3px; transition: all 0.3s ease;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<div class="mt-4">
    <?php require_once BASE_PATH . "/them/app/layout/footer.php"; ?>
</div>
<?php require_once BASE_PATH . "/them/app/layout/js.php"; ?>

<style>
    /* ===== استایل‌های اختصاصی ===== */
    .bge {
        background: linear-gradient(135deg, #0a0e27 0%, #1a1a3e 50%, #0d1b2a 100%);
        border-radius: 0;
    }
    
    .alert-success {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(26, 58, 92, 0.05)) !important;
        border: 2px solid #D4AF37 !important;
        color: #D4AF37 !important;
        text-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
    }
    
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(212, 175, 55, 0.15) !important;
    }
    
    .card-title a:hover {
        color: #D4AF37 !important;
    }
    
    .pagination .page-item .page-link {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 2px solid #D4AF37 !important;
        color: #D4AF37 !important;
        border-radius: 10px !important;
        margin: 0 3px !important;
        transition: all 0.3s ease !important;
        min-width: 40px;
        text-align: center;
    }
    
    .pagination .page-item .page-link:hover {
        background: #D4AF37 !important;
        color: #0a0e27 !important;
        transform: scale(1.05);
        box-shadow: 0 0 30px rgba(212, 175, 55, 0.3);
    }
    
    .pagination .page-item.active .page-link {
        background: #D4AF37 !important;
        color: #0a0e27 !important;
        font-weight: 700;
        box-shadow: 0 0 30px rgba(212, 175, 55, 0.3);
    }
    
    .pagination .page-item.disabled .page-link {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
        pointer-events: none;
    }
    
    /* ===== ریسپانسیو ===== */
    @media (max-width: 768px) {
        .container {
            padding: 0 12px;
        }
        .alert-success {
            font-size: 18px !important;
            padding: 12px !important;
        }
        .card {
            margin-bottom: 15px;
        }
        .card img {
            height: 180px !important;
        }
        .card-title {
            font-size: 14px !important;
        }
        .pagination .page-item .page-link {
            min-width: 35px;
            font-size: 13px;
        }
        .bge {
            padding: 15px !important;
        }
    }
    
    @media (max-width: 480px) {
        .alert-success {
            font-size: 16px !important;
            padding: 10px !important;
        }
        .card img {
            height: 150px !important;
        }
        .card-title {
            font-size: 13px !important;
        }
        .card-text {
            font-size: 12px !important;
        }
        .btn-outline-success {
            font-size: 13px !important;
            padding: 8px !important;
        }
        .pagination .page-item .page-link {
            min-width: 30px;
            font-size: 12px;
            padding: 6px 10px !important;
        }
    }
    
    @media (max-width: 380px) {
        .card img {
            height: 120px !important;
        }
        .alert-success {
            font-size: 14px !important;
        }
    }
</style>

</body>
</html>