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
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="<?php echo isset($post['description']) ? strip_tags($post['description']) : ''; ?>">
    <meta name="keywords" content="<?php echo isset($post['tags']) ? strip_tags($post['tags']) : ''; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <meta property="og:title" content="<?php echo (isset($err) && $err == true) ? (isset($post['title']) ? strip_tags($post['title']) : '') : 'پست مورد نظر پیدا نشد'; ?>">
    <meta property="og:description" content="<?php echo (isset($err) && $err == true) ? (isset($post['description']) ? strip_tags($post['description']) : '') : 'پست مورد نظر پیدا نشد'; ?>">
    <meta property="og:image" content="<?php echo (isset($err) && $err == true && isset($post['img'])) ? assets($post['img']) : ''; ?>">
    <meta property="og:site_name" content="<?php echo isset($dataSeo['title']) ? strip_tags($dataSeo['title']) : ''; ?>">
    <meta property="og:type" content="article">
    <meta property="og:locale" content="fa_IR">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo (isset($err) && $err == true) ? (isset($post['title']) ? strip_tags($post['title']) : '') : 'پست مورد نظر پیدا نشد'; ?>">
    <meta name="twitter:description" content="<?php echo (isset($err) && $err == true) ? (isset($post['description']) ? strip_tags($post['description']) : '') : 'پست مورد نظر پیدا نشد'; ?>">
    <meta name="twitter:image" content="<?php echo (isset($err) && $err == true && isset($post['img'])) ? assets($post['img']) : ''; ?>">
    
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    <link rel="icon" type="image/x-icon" href="<?php echo assets('public/src/img/logo.png'); ?>">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo (isset($err) && $err == true) ? (isset($post['title']) ? strip_tags($post['title']) : '') : 'پست مورد نظر پیدا نشد'; ?> - <?php echo isset($dataSeo['title']) ? strip_tags($dataSeo['title']) : ''; ?></title>
    
    <style>
        /* ============================================================ */
        /* ===== استایل طلایی-مشکی-آبی سه‌بعدی ===== */
        /* ============================================================ */
        
        :root {
            --gold: #D4AF37;
            --gold-light: #F0D060;
            --gold-dark: #B8960F;
            --gold-gradient: linear-gradient(135deg, #D4AF37, #F0D060, #D4AF37);
            --blue-dark: #0a0e27;
            --blue-mid: #1a1a3e;
            --blue-light: #2a5a8c;
            --text-light: #E0E0E0;
            --text-muted: #888;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1a3e 50%, #0d1b2a 100%);
            font-family: 'IRANSans', 'Tahoma', sans-serif;
            color: var(--text-light);
            direction: rtl;
            min-height: 100vh;
            line-height: 1.8;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .row {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .col-12 { width: 100%; }
        .col-md-7 { flex: 0 0 calc(58.33% - 15px); max-width: calc(58.33% - 15px); }
        .col-md-5 { flex: 0 0 calc(41.66% - 15px); max-width: calc(41.66% - 15px); }
        
        @media (max-width: 992px) {
            .col-md-7, .col-md-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .row { gap: 20px; }
        }
        
        /* ===== باکس مقاله ===== */
        .box-content-cities {
            background: rgba(10, 14, 39, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 50px rgba(212, 175, 55, 0.08), inset 0 1px 0 rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-top: 4px solid var(--gold);
            transition: all 0.4s ease;
            animation: fadeIn 0.6s ease;
        }
        
        .box-content-cities:hover {
            box-shadow: 0 15px 60px rgba(212, 175, 55, 0.12), inset 0 1px 0 rgba(212, 175, 55, 0.1);
            transform: translateY(-3px);
            border-color: rgba(212, 175, 55, 0.3);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* ===== تصویر ===== */
        .size-img-city {
            width: 100%;
            height: auto;
            max-height: 420px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid var(--gold);
            box-shadow: 0 0 40px rgba(212, 175, 55, 0.12), 0 10px 30px rgba(0,0,0,0.3);
            transition: all 0.5s ease;
        }
        
        .size-img-city:hover {
            box-shadow: 0 0 60px rgba(212, 175, 55, 0.2), 0 15px 40px rgba(0,0,0,0.4);
            transform: scale(1.01);
        }
        
        /* ===== عنوان ===== */
        .box-content-cities h1 {
            font-size: clamp(24px, 3.5vw, 34px);
            font-weight: 800;
            color: var(--gold);
            margin: 25px 0 15px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--gold);
            line-height: 1.4;
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.15);
        }
        
        .box-content-cities h1::before {
            content: '✦ ';
            color: var(--gold);
            font-size: 22px;
            text-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }
        
        /* ===== محتوا ===== */
        .box-content-cities p {
            font-size: clamp(15px, 1.1vw, 17px);
            line-height: 2.1;
            color: #d0d0d0;
            text-align: justify;
            margin-bottom: 15px;
        }
        
        .box-content-cities p:last-child { margin-bottom: 0; }
        
        /* ===== نوار اطلاعات ===== */
        .info-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px 25px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.06), rgba(10, 14, 39, 0.5));
            border: 1px solid rgba(212, 175, 55, 0.12);
            border-radius: 12px;
            padding: 12px 20px;
            margin: 15px 0 20px;
            backdrop-filter: blur(4px);
        }
        
        .info-bar .info-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            font-size: 14px;
        }
        
        .info-bar .info-item i {
            color: var(--gold);
            font-size: 16px;
        }
        
        .info-bar .info-item .gold-text {
            color: var(--gold);
            font-weight: 700;
        }
        
        .info-bar .info-item a {
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .info-bar .info-item a:hover {
            color: var(--gold);
        }
        
        /* ===== باکس تماس برجسته ===== */
        .contact-box {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), rgba(10, 14, 39, 0.6));
            border: 2px solid rgba(212, 175, 55, 0.2);
            border-radius: 14px;
            padding: 20px 25px;
            margin: 15px 0 20px;
            backdrop-filter: blur(8px);
            box-shadow: 0 0 40px rgba(212, 175, 55, 0.03);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .contact-box:hover {
            border-color: rgba(212, 175, 55, 0.4);
            box-shadow: 0 0 60px rgba(212, 175, 55, 0.06);
        }
        
        .contact-box .brand-name {
            color: var(--gold);
            font-weight: 800;
            font-size: 18px;
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.15);
        }
        
        .contact-box .phone-number {
            display: inline-block;
            text-decoration: none;
            font-size: 26px;
            font-weight: 900;
            color: #D4AF37;
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.15);
            padding: 10px 30px;
            border-radius: 12px;
            background: rgba(212, 175, 55, 0.03);
            transition: all 0.3s ease;
            border: 1px solid rgba(212, 175, 55, 0.1);
            margin: 8px 0;
        }
        
        .contact-box .phone-number:hover {
            background: rgba(212, 175, 55, 0.08);
            box-shadow: 0 0 50px rgba(212, 175, 55, 0.1);
            transform: scale(1.02);
        }
        
        .contact-box .phone-number i {
            margin-left: 12px;
            color: #F0D060;
        }
        
        .contact-box .work-hours {
            color: #aaa;
            font-size: 13px;
            display: block;
            margin-top: 8px;
        }
        
        .contact-box .work-hours i {
            color: var(--gold);
            margin-left: 5px;
        }
        
        /* ===== دکمه ویرایش ===== */
        .btn-success {
            background: var(--gold-gradient) !important;
            border: none !important;
            border-radius: 10px !important;
            padding: 8px 25px !important;
            font-weight: 700 !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.2) !important;
            color: #0a0e27 !important;
        }
        
        .btn-success:hover {
            transform: scale(1.05) !important;
            box-shadow: 0 6px 30px rgba(212, 175, 55, 0.35) !important;
        }
        
        .btn-success a {
            color: #0a0e27 !important;
            text-decoration: none !important;
        }
        
        /* ===== آیکون‌ها ===== */
        .fa, .fas, .bi {
            color: var(--gold);
        }
        
        .text-danger {
            color: #ff6b6b !important;
            font-weight: 700;
        }
        
        .text-info {
            color: var(--gold-light) !important;
            font-weight: 600;
        }
        
        .m-1 { margin: 0.25rem !important; }
        .m-3 { margin: 1rem !important; }
        .mt-2 { margin-top: 0.5rem !important; }
        .mt-3 { margin-top: 1rem !important; }
        .mt-4 { margin-top: 1.5rem !important; }
        .mt-5 { margin-top: 2rem !important; }
        .p-2 { padding: 0.5rem !important; }
        .p-3 { padding: 1rem !important; }
        .d-inline { display: inline !important; }
        .text-decoration-none { text-decoration: none !important; }
        
        /* ===== پیام خطا ===== */
        .alert-danger {
            background: linear-gradient(135deg, rgba(255, 23, 68, 0.15), rgba(139, 0, 0, 0.2));
            border: 2px solid #ff1744;
            color: #ff6b6b;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            backdrop-filter: blur(4px);
        }
        
        .alert-danger h2 {
            margin: 0;
            font-weight: 700;
            font-size: clamp(22px, 3vw, 30px);
        }
        
        .text-primary {
            color: #ff6b6b !important;
        }
        
        /* ===== سایدبار ===== */
        .col-md-5 {
            animation: fadeIn 0.6s ease 0.2s both;
        }
        
        /* ===== اسکرول ===== */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--blue-dark);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--gold-gradient);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-light);
        }
        
        /* ===== ریسپانسیو ===== */
        @media (max-width: 768px) {
            .box-content-cities { padding: 18px; }
            .box-content-cities h1 { font-size: 22px; }
            .box-content-cities p { font-size: 14px; }
            .size-img-city { max-height: 250px; }
            .contact-box .phone-number { font-size: 20px; padding: 8px 16px; }
            .info-bar { padding: 10px 15px; gap: 10px; }
            .info-bar .info-item { font-size: 13px; }
        }
        
        @media (max-width: 480px) {
            .box-content-cities { padding: 12px; }
            .box-content-cities h1 { font-size: 18px; }
            .box-content-cities p { font-size: 13px; }
            .size-img-city { max-height: 180px; }
            .contact-box .phone-number { font-size: 17px; padding: 6px 12px; }
            .contact-box { padding: 15px; }
        }
    </style>
</head>
<body>
<?php require_once BASE_PATH . '/them/app/layout/header.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12 col-md-7">
            <?php if (isset($err) && $err == true) { ?>
                <div class="box-content-cities p-3">
                    
                    <!-- ===== تصویر ===== -->
                    <div class="size-img-city">
                        <img src='<?php echo assets(isset($post['img']) ? $post['img'] : ''); ?>' class="size-img-city" alt="<?php echo isset($post['title']) ? strip_tags($post['title']) : ''; ?>" loading="lazy"/>
                    </div>
                    
                    <!-- ===== نوار اطلاعات ===== -->
                    <div class="info-bar">
                        <span class="info-item">
                            <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                            <span class="gold-text"><?php echo isset($user['name']) ? strip_tags($user['name']) : ''; ?></span>
                        </span>
                        
                        <span class="info-item">
                            <i class="fas fa-eye"></i>
                            <span class="text-danger"><?php echo isset($view) ? (int)$view : 0; ?></span>
                            <span class="text-info">بازدید</span>
                        </span>
                        
                        <span class="info-item">
                            <i class="fas fa-user-tie"></i>
                            <a href="<?php echo assets('profile/'. (isset($user['user_name']) ? $user['user_name'] : '') .'/'. (isset($user['id']) ? $user['id'] : '') ); ?>">مشاهده رزومه</a>
                        </span>
                    </div>
                    
                    <!-- ===== باکس تماس برجسته ===== -->
                    <?php
                    $display_phone = (!empty($post['contact_number'])) ? $post['contact_number'] : (isset($user['phon']) ? $user['phon'] : '');
                    ?>
                    <div class="contact-box">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap;">
                            <span style="background: linear-gradient(135deg, #D4AF37, #F0D060); color: #0a0e27; padding: 4px 16px; border-radius: 30px; font-weight: 700; font-size: 13px; box-shadow: 0 0 30px rgba(212, 175, 55, 0.15);">
                                <i class="fas fa-phone" style="margin-left: 5px;"></i> تماس مستقیم
                            </span>
                            <span class="brand-name"><?php echo isset($post['namebrand']) ? strip_tags($post['namebrand']) : ''; ?></span>
                        </div>
                        
                        <a href="tel:<?php echo $display_phone; ?>" class="phone-number">
                            <i class="fas fa-phone-alt"></i>
                            <?php echo $display_phone; ?>
                        </a>
                        
                        <span class="work-hours">
                            <i class="fas fa-clock"></i>
                            پاسخگویی: ۸ صبح تا ۱۰ شب
                        </span>
                    </div>
                    
                    <!-- ===== دکمه ویرایش ===== -->
                    <?php if (isset($_SESSION['name']) && isset($_SESSION['role']) && $_SESSION['role'] == 1) { ?>
                        <div style="text-align: center; margin: 10px 0 15px;">
                            <button class="btn-success">
                                <a class="nav-link" target="_blank" href="<?php echo assets('admin/posts/update/'. (isset($post['id']) ? $post['id'] : '') ); ?>">
                                    <i class="fas fa-edit"></i> ویرایش
                                </a>
                            </button>
                        </div>
                    <?php } ?>
                    
                    <!-- ===== عنوان و محتوا ===== -->
                    <h1><?php echo isset($post['title']) ? strip_tags($post['title']) : ''; ?></h1>
                    <div class="post-content">
                        <?php echo isset($post['content']) ? $post['content'] : ''; ?>
                    </div>
                    
                </div>
            <?php } else { ?>
                <div class="p-2">
                    <div class="alert-danger p-3">
                        <h2 class="text-primary">⛔ صفحه مورد نظر پیدا نشد</h2>
                        <p style="color: #ff6b6b; margin-top: 10px;">متاسفیم، مقاله‌ای که به دنبال آن هستید وجود ندارد.</p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="col-12 col-md-5">
            <?php require_once BASE_PATH . "/them/app/layout/sidbar.php"; ?>
        </div>
    </div>
</div>

<br>
<?php require_once BASE_PATH . "/them/app/layout/footer.php"; ?>
<?php require_once BASE_PATH . "/them/app/layout/js.php"; ?>
<script>
    $(document).ready(function() {
        $("#next-info").on("click", function(e) {
            // کد خودت اینجا
        });
    });
</script>
</body>
</html>
<?php
ob_end_flush();
?>