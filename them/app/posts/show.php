<?php
ob_start("minifier");

function minifier($code)
{
    $search = array(
        '/\>[^\S ]+/s',
        '/[^\S ]+\</s',
        '/(\s)+/s',
        '/<!--(.|\s)*?-->/'
    );
    $replace = array('>', '<', '\\1');
    $code = preg_replace($search, $replace, $code);
    return $code;
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $post['description'] ?? '' ?>">
    <meta name="keywords" content="<?= $post['tags'] ?? '' ?>">
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <link rel="icon" type="image/x-icon" href="<?= assets('public/src/img/logo.png') ?>">
    <title><?= $err == true ? $post['title'] : 'پست مورد نظر پیدا نشد' ?> - <?= $dataSeo['title'] ?? '' ?></title>
    
    <style>
        /* ===== استایل‌های جدید صفحه مقاله ===== */
        
        /* تنظیمات کلی */
        .article-wrapper {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 30px 0;
        }
        
        .article-main {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.06);
            padding: 40px;
            overflow: hidden;
        }
        
        /* هدر مقاله */
        .article-header {
            margin-bottom: 30px;
        }
        
        .article-badge {
            display: inline-block;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            padding: 5px 20px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .article-title {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1a1a2e;
            line-height: 1.3;
            margin: 15px 0 20px;
        }
        
        /* متا اطلاعات */
        .article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 15px 0;
            border-top: 2px solid #f0f2f5;
            border-bottom: 2px solid #f0f2f5;
            margin-bottom: 25px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        .meta-item i {
            color: #0d6efd;
            font-size: 1.1rem;
            width: 20px;
        }
        
        .meta-item a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }
        
        .meta-item a:hover {
            color: #0a58ca;
            text-decoration: underline;
        }
        
        .meta-item .view-count {
            color: #dc3545;
            font-weight: 700;
        }
        
        /* تصویر مقاله */
        .article-image {
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 30px;
            background: #f0f2f5;
        }
        
        .article-image img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .article-image img:hover {
            transform: scale(1.02);
        }
        
        /* محتوای مقاله */
        .article-content {
            font-size: 1.1rem;
            line-height: 2;
            color: #2d3436;
            padding: 20px 0;
        }
        
        .article-content p {
            margin-bottom: 20px;
        }
        
        .article-content h2, 
        .article-content h3 {
            font-weight: 700;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #1a1a2e;
        }
        
        .article-content ul, 
        .article-content ol {
            padding-right: 25px;
            margin-bottom: 20px;
        }
        
        .article-content img {
            max-width: 100%;
            border-radius: 12px;
            margin: 20px 0;
        }
        
        .article-content blockquote {
            background: #f0f2f5;
            border-right: 4px solid #0d6efd;
            padding: 20px 25px;
            border-radius: 12px;
            margin: 20px 0;
            font-style: italic;
            color: #555;
        }
        
        /* دکمه‌های پایین مقاله */
        .article-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #f0f2f5;
        }
        
        .article-actions .btn {
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .article-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        /* بخش نویسنده */
        .author-box {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 25px 30px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .author-box .author-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        
        .author-box .author-info {
            flex: 1;
        }
        
        .author-box .author-info h5 {
            font-weight: 700;
            margin-bottom: 5px;
            color: #1a1a2e;
        }
        
        .author-box .author-info p {
            color: #6c757d;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .author-box .author-info .btn {
            margin-top: 8px;
            padding: 5px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
        }
        
        /* سایدبار */
        .sidebar-wrapper {
            position: sticky;
            top: 100px;
        }
        
        .sidebar-box {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .sidebar-box h5 {
            font-weight: 800;
            color: #1a1a2e;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 12px;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        /* ریسپانسیو */
        @media (max-width: 992px) {
            .article-main {
                padding: 25px;
            }
            
            .article-title {
                font-size: 2rem;
            }
            
            .sidebar-wrapper {
                position: static;
                margin-top: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .article-main {
                padding: 15px;
                border-radius: 16px;
            }
            
            .article-title {
                font-size: 1.5rem;
            }
            
            .article-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .article-content {
                font-size: 1rem;
                line-height: 1.8;
            }
            
            .author-box {
                flex-direction: column;
                text-align: center;
            }
            
            .article-actions .btn {
                width: 100%;
                text-align: center;
            }
        }
        
        @media (max-width: 400px) {
            .article-main {
                padding: 10px;
            }
            
            .article-title {
                font-size: 1.3rem;
            }
            
            .meta-item {
                font-size: 0.85rem;
            }
        }
        
        /* انیمیشن‌ها */
        .article-main {
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Scrollbar سفارشی */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #0d6efd;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #0a58ca;
        }
    </style>
</head>
<body>

<?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>

<!-- ===== صفحه مقاله ===== -->
<div class="article-wrapper">
    <div class="container">
        <div class="row g-4">
            
            <!-- ستون اصلی مقاله -->
            <div class="col-12 col-lg-8">
                <?php if ($err) { ?>
                    <div class="article-main">
                        
                        <!-- هدر مقاله -->
                        <div class="article-header">
                            <span class="article-badge">
                                <i class="fas fa-file-alt"></i> مقاله
                            </span>
                            <h1 class="article-title"><?= $post['title'] ?? '' ?></h1>
                        </div>
                        
                        <!-- متا اطلاعات -->
                        <div class="article-meta">
                            <span class="meta-item">
                                <i class="fas fa-user"></i>
                                <a href="<?= assets('profile/'. $user['user_name'] ?? '' .'/'. $user['id'] ?? '') ?>">
                                    <?= $user['name'] ?? 'کاربر' ?>
                                </a>
                            </span>
                            
                            <span class="meta-item">
                                <i class="fas fa-phone"></i>
                                <?php 
                                $display_phone = !empty($post['contact_number']) ? $post['contact_number'] : ($user['phon'] ?? '');
                                ?>
                                <a href="tel:<?= $display_phone ?>"><?= $display_phone ?></a>
                            </span>
                            
                            <span class="meta-item">
                                <i class="fas fa-eye"></i>
                                <span class="view-count"><?= $view ?? 0 ?></span> بازدید
                            </span>
                            
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1) { ?>
                                <span class="meta-item">
                                    <a href="<?= assets('admin/posts/update/'.$post['id'] ?? '') ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-edit"></i> ویرایش
                                    </a>
                                </span>
                            <?php } ?>
                        </div>
                        
                        <!-- تصویر مقاله -->
                        <?php if (!empty($post['img'])) { ?>
                            <div class="article-image">
                                <img src="<?= assets($post['img']) ?>" alt="<?= $post['title'] ?? '' ?>">
                            </div>
                        <?php } ?>
                        
                        <!-- محتوای مقاله -->
                        <div class="article-content">
                            <?= $post['content'] ?? '' ?>
                        </div>
                        
                        <!-- دکمه‌های اشتراک‌گذاری -->
                        <div class="article-actions">
                            <button class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> چاپ
                            </button>
                            <button class="btn btn-outline-success" onclick="shareArticle()">
                                <i class="fas fa-share-alt"></i> اشتراک‌گذاری
                            </button>
                            <a href="<?= assets('/') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right"></i> بازگشت
                            </a>
                        </div>
                        
                        <!-- بخش نویسنده -->
                        <div class="author-box">
                            <div class="author-avatar">
                                <?= mb_substr($user['name'] ?? 'U', 0, 1) ?>
                            </div>
                            <div class="author-info">
                                <h5><?= $user['name'] ?? 'کاربر' ?></h5>
                                <p>
                                    <i class="fas fa-phone"></i> <?= $display_phone ?>
                                </p>
                                <a href="<?= assets('profile/'. $user['user_name'] ?? '' .'/'. $user['id'] ?? '') ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-user"></i> مشاهده رزومه
                                </a>
                            </div>
                        </div>
                        
                    </div>
                <?php } else { ?>
                    <div class="alert alert-danger text-center p-5">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h3>صفحه مورد نظر پیدا نشد</h3>
                        <p>متأسفیم، مقاله‌ای با این شناسه وجود ندارد.</p>
                        <a href="<?= assets('/') ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-home"></i> بازگشت به صفحه اصلی
                        </a>
                    </div>
                <?php } ?>
            </div>
            
            <!-- سایدبار -->
            <div class="col-12 col-lg-4">
                <div class="sidebar-wrapper">
                    <?php require_once BASE_PATH . "/them/app/layout/sidbar.php"; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php require_once BASE_PATH . "/them/app/layout/footer.php"; ?>
<?php require_once BASE_PATH . "/them/app/layout/js.php"; ?>

<script>
// اشتراک‌گذاری
function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: '<?= addslashes($post['title'] ?? '') ?>',
            text: '<?= addslashes(strip_tags($post['description'] ?? '')) ?>',
            url: window.location.href
        }).catch(() => {});
    } else {
        // کپی لینک
        navigator.clipboard.writeText(window.location.href).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'لینک کپی شد!',
                text: 'لینک این مقاله در کلیپ‌بورد شما کپی شد.',
                timer: 2000,
                showConfirmButton: false
            });
        }).catch(() => {
            // روش جایگزین برای کپی
            const input = document.createElement('input');
            input.value = window.location.href;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('لینک کپی شد!');
        });
    }
}

// انیمیشن اسکرول
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    document.querySelectorAll('.article-main, .sidebar-box').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });
});
</script>

</body>
</html>
<?php
ob_end_flush();
?>