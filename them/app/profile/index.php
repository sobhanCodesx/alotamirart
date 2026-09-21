<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    
    <!-- ===== سئو ===== -->
    <title><?php echo isset($user['name']) ? strip_tags($user['name']) : ''; ?> | رزومه حرفه‌ای</title>
    <meta name="description" content="<?php echo isset($user['bio']) ? strip_tags($user['bio']) : ''; ?>">
    <meta name="keywords" content="<?php echo isset($user['title']) ? strip_tags($user['title']) : ''; ?>, رزومه, نمونه کار, برنامه نویس">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- ===== Open Graph ===== -->
    <meta property="og:title" content="<?php echo isset($user['name']) ? strip_tags($user['name']) : ''; ?> | رزومه حرفه‌ای">
    <meta property="og:description" content="<?php echo isset($user['bio']) ? strip_tags($user['bio']) : ''; ?>">
    <meta property="og:image" content="<?php echo isset($user['img']) ? assets($user['img']) : ''; ?>">
    <meta property="og:type" content="profile">
    <meta property="og:locale" content="fa_IR">
    
    <?php require_once BASE_PATH . '/them/app/layout/heading.php'; ?>
    
    <style>
        /* ============================================================ */
        /* ===== تم طلایی-مشکی-آبی سه‌بعدی ===== */
        /* ============================================================ */
        
        :root {
            --gold: #D4AF37;
            --gold-light: #F0D060;
            --gold-dark: #B8960F;
            --gold-glow: 0 0 40px rgba(212, 175, 55, 0.15);
            --blue-dark: #0a0e27;
            --blue-mid: #1a1a3e;
            --blue-light: #2a5a8c;
            --text-light: #E0E0E0;
            --shadow-gold: 0 10px 50px rgba(212, 175, 55, 0.08);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        html { scroll-behavior: smooth; }
        
        body {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1a3e 50%, #0d1b2a 100%);
            font-family: 'IRANSans', 'Tahoma', 'Segoe UI', sans-serif;
            color: var(--text-light);
            direction: rtl;
            min-height: 100vh;
            line-height: 1.8;
            overflow-x: hidden;
        }
        
        /* ============================================================ */
        /* ===== هدر و سایدبار ===== */
        /* ============================================================ */
        
        #header {
            background: rgba(10, 14, 39, 0.92) !important;
            backdrop-filter: blur(16px);
            border-left: 2px solid rgba(212, 175, 55, 0.12);
            box-shadow: 4px 0 40px rgba(0,0,0,0.4);
            width: 280px !important;
            transition: all 0.4s ease;
        }
        
        #header:hover {
            border-left-color: rgba(212, 175, 55, 0.3);
        }
        
        #header .profile {
            text-align: center;
            padding: 30px 15px;
        }
        
        #header .profile img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 4px solid var(--gold);
            box-shadow: 0 0 50px rgba(212, 175, 55, 0.12), inset 0 0 30px rgba(212, 175, 55, 0.05);
            transition: all 0.5s ease;
            object-fit: cover;
        }
        
        #header .profile img:hover {
            transform: scale(1.05) rotate(-3deg);
            box-shadow: 0 0 80px rgba(212, 175, 55, 0.2);
        }
        
        #header .profile h1 {
            margin-top: 15px;
            font-size: 22px;
            font-weight: 800;
        }
        
        #header .profile h1 a {
            color: var(--gold) !important;
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.15);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        #header .profile h1 a:hover {
            text-shadow: 0 0 50px rgba(212, 175, 55, 0.3);
            color: var(--gold-light) !important;
        }
        
        /* ===== شبکه‌های اجتماعی ===== */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 18px;
            flex-wrap: wrap;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.06);
            border: 1px solid rgba(212, 175, 55, 0.1);
            color: var(--gold);
            font-size: 20px;
            transition: all 0.4s ease;
            text-decoration: none;
        }
        
        .social-links a:hover {
            background: var(--gold-gradient);
            color: #0a0e27 !important;
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.25);
            border-color: transparent;
        }
        
        /* ===== منو ===== */
        .nav-menu {
            padding: 10px 0;
        }
        
        .nav-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-menu ul li {
            margin: 2px 0;
        }
        
        .nav-menu ul li a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #a0a0a0;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            border-right: 3px solid transparent;
            transition: all 0.3s ease;
            gap: 12px;
        }
        
        .nav-menu ul li a i {
            color: var(--gold);
            font-size: 20px;
            min-width: 24px;
            transition: all 0.3s ease;
        }
        
        .nav-menu ul li a:hover,
        .nav-menu ul li a.active {
            color: var(--gold);
            background: rgba(212, 175, 55, 0.04);
            border-right-color: var(--gold);
        }
        
        .nav-menu ul li a:hover i,
        .nav-menu ul li a.active i {
            transform: scale(1.1);
        }
        
        /* ============================================================ */
        /* ===== بخش Hero ===== */
        /* ============================================================ */
        
        #hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover !important;
            background-position: center !important;
            background-attachment: fixed !important;
        }
        
        #hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 14, 39, 0.65);
            backdrop-filter: blur(3px);
        }
        
        #hero .hero-container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 20px;
        }
        
        #hero .hero-container h1 {
            font-size: clamp(36px, 6vw, 64px);
            font-weight: 900;
            color: var(--gold);
            text-shadow: 0 0 60px rgba(212, 175, 55, 0.15);
            margin-bottom: 15px;
            animation: fadeInDown 1s ease;
        }
        
        #hero .hero-container .typed {
            font-size: clamp(18px, 2.5vw, 30px);
            color: #e0e0e0;
            font-weight: 300;
        }
        
        #hero .hero-container .typed-cursor {
            color: var(--gold);
        }
        
        .hero-badge {
            display: inline-block;
            background: rgba(212, 175, 55, 0.08);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 30px;
            padding: 8px 25px;
            color: var(--gold);
            font-size: 14px;
            margin-top: 20px;
            backdrop-filter: blur(4px);
        }
        
        /* ============================================================ */
        /* ===== بخش‌ها ===== */
        /* ============================================================ */
        
        section {
            padding: 80px 0;
            position: relative;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: clamp(26px, 3vw, 36px);
            font-weight: 800;
            color: var(--gold);
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.1);
            display: inline-block;
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold));
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.2);
        }
        
        .section-title p {
            color: #a0a0a0;
            font-size: 16px;
            max-width: 700px;
            margin: 15px auto 0;
            line-height: 1.9;
        }
        
        /* ============================================================ */
        /* ===== کارت‌ها ===== */
        /* ============================================================ */
        
        .card-glass {
            background: rgba(10, 14, 39, 0.5);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(212, 175, 55, 0.06);
            border-radius: 16px;
            padding: 25px;
            transition: all 0.4s ease;
            height: 100%;
        }
        
        .card-glass:hover {
            transform: translateY(-8px);
            border-color: rgba(212, 175, 55, 0.15);
            box-shadow: 0 20px 60px rgba(212, 175, 55, 0.04);
        }
        
        .card-glass img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid rgba(212, 175, 55, 0.1);
            transition: all 0.5s ease;
        }
        
        .card-glass img:hover {
            transform: scale(1.02);
            border-color: var(--gold);
            box-shadow: 0 0 40px rgba(212, 175, 55, 0.1);
        }
        
        .card-glass h4 {
            color: var(--gold);
            font-weight: 700;
            margin-top: 15px;
            font-size: 18px;
        }
        
        .card-glass p {
            color: #b0b0b0;
            font-size: 14px;
            line-height: 1.8;
        }
        
        .card-glass .btn-gold {
            display: inline-block;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #0a0e27;
            padding: 8px 25px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 10px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        
        .card-glass .btn-gold:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.25);
        }
        
        /* ============================================================ */
        /* ===== شمارنده‌ها ===== */
        /* ============================================================ */
        
        .count-box {
            text-align: center;
            background: rgba(10, 14, 39, 0.4);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(212, 175, 55, 0.06);
            border-radius: 16px;
            padding: 30px 20px;
            transition: all 0.4s ease;
        }
        
        .count-box:hover {
            transform: translateY(-5px);
            border-color: rgba(212, 175, 55, 0.15);
            box-shadow: 0 10px 40px rgba(212, 175, 55, 0.04);
        }
        
        .count-box i {
            font-size: 36px;
            color: var(--gold);
            display: block;
            margin-bottom: 10px;
        }
        
        .count-box span {
            font-size: 32px;
            font-weight: 900;
            color: var(--gold);
            display: block;
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.1);
        }
        
        .count-box p {
            color: #b0b0b0;
            font-size: 14px;
            margin-top: 5px;
            font-weight: 500;
        }
        
        /* ============================================================ */
        /* ===== فرم تماس ===== */
        /* ============================================================ */
        
        .form-control-gold {
            background: rgba(10, 14, 39, 0.6) !important;
            border: 2px solid rgba(212, 175, 55, 0.08) !important;
            color: #e0e0e0 !important;
            border-radius: 12px !important;
            padding: 14px 18px !important;
            transition: all 0.3s ease !important;
            width: 100%;
        }
        
        .form-control-gold:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 40px rgba(212, 175, 55, 0.04) !important;
            outline: none !important;
            background: rgba(10, 14, 39, 0.8) !important;
        }
        
        .form-control-gold::placeholder {
            color: #666 !important;
        }
        
        .form-control-gold label {
            color: var(--gold);
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }
        
        .btn-submit-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #0a0e27;
            border: none;
            border-radius: 12px;
            padding: 14px 45px;
            font-weight: 800;
            font-size: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 25px rgba(212, 175, 55, 0.15);
            width: 100%;
        }
        
        .btn-submit-gold:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 40px rgba(212, 175, 55, 0.25);
        }
        
        /* ============================================================ */
        /* ===== اطلاعات تماس ===== */
        /* ============================================================ */
        
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 18px 20px;
            background: rgba(10, 14, 39, 0.3);
            border-radius: 12px;
            border: 1px solid rgba(212, 175, 55, 0.04);
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        
        .info-item:hover {
            border-color: rgba(212, 175, 55, 0.1);
            background: rgba(10, 14, 39, 0.5);
        }
        
        .info-item i {
            font-size: 24px;
            color: var(--gold);
            min-width: 40px;
            text-align: center;
        }
        
        .info-item h4 {
            color: var(--gold);
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 2px;
        }
        
        .info-item p,
        .info-item a {
            color: #b0b0b0;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .info-item a:hover {
            color: var(--gold);
        }
        
        /* ============================================================ */
        /* ===== فوتر ===== */
        /* ============================================================ */
        
        #footer {
            background: rgba(10, 14, 39, 0.9);
            border-top: 1px solid rgba(212, 175, 55, 0.05);
            padding: 25px 0;
            text-align: center;
        }
        
        #footer .credits {
            color: #666;
            font-size: 14px;
        }
        
        #footer .credits a {
            color: var(--gold);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        #footer .credits a:hover {
            color: var(--gold-light);
        }
        
        /* ============================================================ */
        /* ===== دکمه بازگشت ===== */
        /* ============================================================ */
        
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #0a0e27;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            text-decoration: none;
            box-shadow: 0 4px 30px rgba(212, 175, 55, 0.15);
            transition: all 0.3s ease;
            z-index: 999;
            border: none;
        }
        
        .back-to-top:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 50px rgba(212, 175, 55, 0.25);
            color: #0a0e27;
        }
        
        /* ============================================================ */
        /* ===== انیمیشن‌ها ===== */
        /* ============================================================ */
        
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulseGold {
            0%, 100% { box-shadow: 0 0 40px rgba(212, 175, 55, 0.1); }
            50% { box-shadow: 0 0 60px rgba(212, 175, 55, 0.2); }
        }
        
        /* ============================================================ */
        /* ===== ریسپانسیو ===== */
        /* ============================================================ */
        
        @media (max-width: 992px) {
            #header { width: 100% !important; height: auto; position: relative; }
            #hero { min-height: 70vh; }
            section { padding: 60px 0; }
        }
        
        @media (max-width: 768px) {
            #header .profile img { width: 100px; height: 100px; }
            #hero .hero-container h1 { font-size: 28px; }
            .count-box span { font-size: 24px; }
            section { padding: 40px 0; }
        }
        
        @media (max-width: 480px) {
            #header .profile img { width: 80px; height: 80px; }
            #hero .hero-container h1 { font-size: 22px; }
            .card-glass img { height: 150px; }
            .count-box { padding: 20px 15px; }
            .count-box span { font-size: 20px; }
        }
        
        /* ============================================================ */
        /* ===== اسکرول ===== */
        /* ============================================================ */
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0e27; border-radius: 10px; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--gold), var(--gold-light));
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--gold-dark); }
    </style>
</head>
<body>

    <!-- ============================================================ -->
    <!-- ===== هدر ===== -->
    <!-- ============================================================ -->
    
    <header id="header">
        <div class="d-flex flex-column">
            <div class="profile">
                <img src="<?php echo assets(isset($user['img']) ? $user['img'] : ''); ?>" alt="<?php echo isset($user['name']) ? strip_tags($user['name']) : ''; ?>">
                <h1><a href="#hero"><?php echo isset($user['name']) ? strip_tags($user['name']) : ''; ?></a></h1>
                
                <div class="social-links">
                    <?php if(!empty($user['instagram'])): ?>
                        <a href="https://instagram.com/<?php echo strip_tags($user['instagram']); ?>" target="_blank"><i class="bx bxl-instagram"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($user['telegram'])): ?>
                        <a href="https://t.me/<?php echo strip_tags($user['telegram']); ?>" target="_blank"><i class="bx bxl-telegram"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($user['whatsapp'])): ?>
                        <a href="https://wa.me/<?php echo strip_tags($user['whatsapp']); ?>" target="_blank"><i class="bx bxl-whatsapp"></i></a>
                    <?php endif; ?>
                    <?php if(!empty($user['linkedin'])): ?>
                        <a href="<?php echo strip_tags($user['linkedin']); ?>" target="_blank"><i class="bx bxl-linkedin"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <nav class="nav-menu">
                <ul>
                    <li><a href="#hero" class="active"><i class="bx bx-home"></i> صفحه اصلی</a></li>
                    <li><a href="#about"><i class="bx bx-user"></i> درباره من</a></li>
                    <li><a href="#resume"><i class="bx bx-file-blank"></i> مقالات</a></li>
                    <li><a href="#brands"><i class="bx bx-server"></i> برندها</a></li>
                    <li><a href="#contact"><i class="bx bx-envelope"></i> ارتباط با من</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- ============================================================ -->
    <!-- ===== بخش Hero ===== -->
    <!-- ============================================================ -->
    
    <section id="hero" style="background-image:url('<?php echo assets(isset($user['img']) ? $user['img'] : ''); ?>')">
        <div class="hero-container">
            <h1><?php echo isset($user['name']) ? strip_tags($user['name']) : ''; ?></h1>
            <p>
                <span class="typed" data-typed-items="<?php echo isset($user['title']) ? strip_tags($user['title']) : ''; ?>"></span>
            </p>
            <div class="hero-badge">
                <i class="bx bx-check-circle"></i> آماده همکاری
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ===== درباره من ===== -->
    <!-- ============================================================ -->
    
    <section id="about">
        <div class="container">
            <div class="section-title">
                <h2>درباره من</h2>
                <p><?php echo isset($user['bio']) ? strip_tags($user['bio']) : ''; ?></p>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <img src="<?php echo assets(isset($user['img']) ? $user['img'] : ''); ?>" class="img-fluid rounded" style="border: 4px solid var(--gold); box-shadow: 0 0 50px rgba(212,175,55,0.1); width: 100%;" alt="<?php echo isset($user['name']) ? strip_tags($user['name']) : ''; ?>">
                </div>
                <div class="col-lg-8">
                    <h3 style="color: var(--gold); margin-bottom: 15px;"><?php echo isset($user['title']) ? strip_tags($user['title']) : ''; ?></h3>
                    <div class="row">
                        <div class="col-lg-6">
                            <p><i class="bx bx-chevron-left" style="color: var(--gold);"></i> <strong>متولد:</strong> <?php echo isset($user['birt']) ? strip_tags($user['birt']) : ''; ?></p>
                            <p><i class="bx bx-chevron-left" style="color: var(--gold);"></i> <strong>شهر:</strong> <?php echo isset($user['city']) ? strip_tags($user['city']) : ''; ?></p>
                            <p><i class="bx bx-chevron-left" style="color: var(--gold);"></i> <strong>شماره:</strong> <a href="tel:<?php echo isset($user['phon']) ? strip_tags($user['phon']) : ''; ?>" style="color: #b0b0b0; text-decoration: none;"><?php echo isset($user['phon']) ? strip_tags($user['phon']) : ''; ?></a></p>
                        </div>
                        <div class="col-lg-6">
                            <p><i class="bx bx-chevron-left" style="color: var(--gold);"></i> <strong>سن:</strong> <?php echo isset($user['age']) ? strip_tags($user['age']) : ''; ?></p>
                            <p><i class="bx bx-chevron-left" style="color: var(--gold);"></i> <strong>مدرک:</strong> <?php echo isset($user['crti']) ? strip_tags($user['crti']) : ''; ?></p>
                            <p><i class="bx bx-chevron-left" style="color: var(--gold);"></i> <strong>ایمیل:</strong> <a href="mailto:<?php echo isset($user['email']) ? strip_tags($user['email']) : ''; ?>" style="color: #b0b0b0; text-decoration: none;"><?php echo isset($user['email']) ? strip_tags($user['email']) : ''; ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ===== آمار ===== -->
    <!-- ============================================================ -->
    
    <section id="facts" style="background: rgba(10,14,39,0.3); padding: 60px 0;">
        <div class="container">
            <div class="section-title">
                <h2>آمار کلی</h2>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="count-box">
                        <i class="bx bx-smile"></i>
                        <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
                        <p>مشتری خوشحال</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="count-box">
                        <i class="bx bx-file"></i>
                        <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
                        <p>پروژه انجام شده</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="count-box">
                        <i class="bx bx-time"></i>
                        <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1" class="purecounter"></span>
                        <p>ساعت کار</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="count-box">
                        <i class="bx bx-group"></i>
                        <span data-purecounter-start="0" data-purecounter-end="32" data-purecounter-duration="1" class="purecounter"></span>
                        <p>مشتری ثابت</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ===== مقالات ===== -->
    <!-- ============================================================ -->
    
    <section id="resume">
        <div class="container">
            <div class="section-title">
                <h2>آخرین مقالات</h2>
                <p>جدیدترین نوشته‌های من</p>
            </div>
            <div class="row">
                <?php foreach($posts as $p){ ?>
                <div class="col-lg-6 mb-4">
                    <div class="card-glass">
                        <img src="<?php echo assets($p['img']); ?>" alt="<?php echo isset($p['title']) ? strip_tags($p['title']) : ''; ?>">
                        <h4><?php echo isset($p['title']) ? strip_tags($p['title']) : ''; ?></h4>
                        <p><?php echo isset($p['description']) ? strip_tags($p['description']) : ''; ?></p>
                        <p style="font-size: 13px; color: #666;"><i class="bx bx-show"></i> <?php echo isset($p['view']) ? (int)$p['view'] : 0; ?> بازدید</p>
                        <a href="<?php echo assets('post/' . $p['id']); ?>" class="btn-gold">مطالعه بیشتر</a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ===== برندها ===== -->
    <!-- ============================================================ -->
    
    <section id="brands" style="background: rgba(10,14,39,0.3);">
        <div class="container">
            <div class="section-title">
                <h2>برندهای همکار</h2>
                <p>برندهایی که با آنها همکاری داشته‌ام</p>
            </div>
            <div class="row">
                <?php foreach($brand as $p){ ?>
                <div class="col-lg-6 mb-4">
                    <div class="card-glass">
                        <img src="<?php echo assets($p['img']); ?>" alt="<?php echo isset($p['title']) ? strip_tags($p['title']) : ''; ?>">
                        <h4><?php echo isset($p['title']) ? strip_tags($p['title']) : ''; ?></h4>
                        <p><?php echo isset($p['des']) ? strip_tags($p['des']) : ''; ?></p>
                        <p style="font-size: 13px; color: #666;"><i class="bx bx-show"></i> <?php echo isset($p['view']) ? (int)$p['view'] : 0; ?> بازدید</p>
                        <a href="<?php echo assets($p['slug']."/" . $p['id']); ?>" class="btn-gold">مشاهده برند</a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ===== تماس ===== -->
    <!-- ============================================================ -->
    
    <section id="contact">
        <div class="container">
            <div class="section-title">
                <h2>ارتباط با من</h2>
                <p>برای همکاری یا مشاوره با من در ارتباط باشید</p>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    <div class="info-item">
                        <i class="bx bx-map"></i>
                        <div>
                            <h4>آدرس</h4>
                            <p><?php echo isset($user['address']) ? strip_tags($user['address']) : ''; ?></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="bx bx-envelope"></i>
                        <div>
                            <h4>ایمیل</h4>
                            <a href="mailto:<?php echo isset($user['email']) ? strip_tags($user['email']) : ''; ?>"><?php echo isset($user['email']) ? strip_tags($user['email']) : ''; ?></a>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="bx bx-phone"></i>
                        <div>
                            <h4>تلفن</h4>
                            <a href="tel:<?php echo isset($user['phon']) ? strip_tags($user['phon']) : ''; ?>"><?php echo isset($user['phon']) ? strip_tags($user['phon']) : ''; ?></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <form action="forms/contact.php" method="post" class="php-email-form">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>نام و نام خانوادگی</label>
                                <input type="text" name="name" class="form-control-gold" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>ایمیل</label>
                                <input type="email" name="email" class="form-control-gold" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>موضوع</label>
                            <input type="text" name="subject" class="form-control-gold" required>
                        </div>
                        <div class="form-group">
                            <label>پیام شما</label>
                            <textarea name="message" class="form-control-gold" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn-submit-gold">ارسال پیام</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ===== فوتر ===== -->
    <!-- ============================================================ -->
    
    <footer id="footer">
        <div class="container">
            <div class="credits">
                طراحی شده با ❤️ توسط <a href="#"><?php echo isset($user['name']) ? strip_tags($user['name']) : ''; ?></a>
            </div>
        </div>
    </footer>

    <!-- ============================================================ -->
    <!-- ===== دکمه بازگشت ===== -->
    <!-- ============================================================ -->
    
    <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>

    <!-- ============================================================ -->
    <!-- ===== اسکریپت‌ها ===== -->
    <!-- ============================================================ -->
    
    <script src="<?php echo assets('them/app/profile/assets/vendor/purecounter/purecounter_vanilla.js'); ?>"></script>
    <script src="<?php echo assets('them/app/profile/assets/vendor/typed.js/typed.umd.js'); ?>"></script>
    <script src="<?php echo assets('them/app/profile/assets/vendor/aos/aos.js'); ?>"></script>
    <script src="<?php echo assets('them/app/profile/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo assets('them/app/profile/assets/vendor/glightbox/js/glightbox.min.js'); ?>"></script>
    <script src="<?php echo assets('them/app/profile/assets/vendor/isotope-layout/isotope.pkgd.min.js'); ?>"></script>
    <script src="<?php echo assets('them/app/profile/assets/vendor/swiper/swiper-bundle.min.js'); ?>"></script>
    <script src="<?php echo assets('them/app/profile/assets/vendor/waypoints/noframework.waypoints.js'); ?>"></script>
    <script src="<?php echo assets('them/app/profile/assets/js/main.js'); ?>"></script>

</body>
</html>