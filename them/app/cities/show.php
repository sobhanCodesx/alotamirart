<?php
// ============================================================
// اطلاعات شهر
// ============================================================
$cityName = $city['name'];
$citySlug = $city['slug'];
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>خدمات تعمیرات در شهر <?= htmlspecialchars($cityName) ?> | الو تعمیراتچی</title>
    <meta name="description" content="خدمات تخصصی تعمیرات یخچال، لباسشویی، کولر گازی، تلویزیون، اجاق گاز و ظرفشویی در شهر <?= htmlspecialchars($cityName) ?> با بهترین کیفیت و ضمانت" />
    
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%);
            min-height: 100vh;
            direction: rtl;
            padding: 30px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            border-radius: 30px;
            padding: 40px 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.06);
        }
        .header {
            text-align: center;
            padding: 20px 0 30px;
            border-bottom: 4px solid #facc15;
            margin-bottom: 30px;
        }
        .header .city-icon { font-size: 4rem; display: block; margin-bottom: 10px; }
        .header h1 { font-size: 2.5rem; font-weight: 900; color: #0f172a; }
        .header h1 .highlight { color: #facc15; }
        .header p { font-size: 1.1rem; color: #64748b; margin-top: 6px; }
        .header .badge {
            display: inline-block;
            background: #f1f5f9;
            padding: 4px 20px;
            border-radius: 30px;
            font-size: 0.8rem;
            color: #475569;
            margin-top: 10px;
        }
        
        .services {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0 30px;
        }
        .service-item {
            background: #f8fafc;
            border-radius: 20px;
            padding: 25px 20px;
            text-align: center;
            border: 2px solid #e9edf2;
            transition: 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .service-item:hover {
            border-color: #facc15;
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(250,204,21,0.1);
        }
        .service-item .icon { font-size: 3rem; display: block; margin-bottom: 8px; }
        .service-item h3 { font-size: 1.2rem; font-weight: 800; color: #0f172a; }
        .service-item p { font-size: 0.9rem; color: #64748b; margin-top: 4px; }
        .service-item .btn-small {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 20px;
            background: #facc15;
            color: #0f172a;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        
        .contact-row {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin: 30px 0 20px;
            flex-wrap: wrap;
        }
        .contact-row .btn {
            padding: 14px 36px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'Vazirmatn', sans-serif;
            transition: 0.3s;
        }
        .contact-row .btn-phone {
            background: #0f172a;
            color: #fff;
            border: 2px solid #0f172a;
        }
        .contact-row .btn-phone:hover {
            background: #facc15;
            color: #0f172a;
            border-color: #facc15;
        }
        .contact-row .btn-whatsapp {
            background: #25D366;
            color: #fff;
            border: 2px solid #25D366;
        }
        .contact-row .btn-whatsapp:hover {
            background: #1da851;
            border-color: #1da851;
        }
        
        .back-link {
            display: block;
            text-align: center;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
        }
        .back-link:hover { color: #facc15; }
        
        /* ===== دکمه شناور تماس ===== */
        .floating-phone {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #ffffff;
            padding: 14px 24px;
            border-radius: 60px;
            text-decoration: none;
            font-family: 'Vazirmatn', 'Segoe UI', Tahoma, sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.35);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            cursor: pointer;
            animation: pulse-phone 2s infinite;
        }
        .floating-phone:hover {
            transform: scale(1.08) translateY(-4px);
            box-shadow: 0 16px 50px rgba(37, 211, 102, 0.45);
            color: #ffffff;
        }
        .floating-phone i { font-size: 1.8rem; }
        .floating-phone .phone-text { display: inline-block; }
        .floating-phone .phone-number {
            background: rgba(255,255,255,0.15);
            padding: 2px 14px;
            border-radius: 40px;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        @keyframes pulse-phone {
            0%, 100% { box-shadow: 0 8px 30px rgba(37, 211, 102, 0.35); }
            50% { box-shadow: 0 8px 50px rgba(37, 211, 102, 0.6); }
        }
        
        .back-to-top {
            position: fixed;
            bottom: 100px;
            right: 30px;
            z-index: 9998;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #facc15;
            border: 2px solid #facc15;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transition: all 0.4s ease;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            cursor: pointer;
        }
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .back-to-top:hover {
            background: #facc15;
            color: #0f172a;
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(250,204,21,0.3);
        }
        
        @media (max-width: 768px) {
            .container { padding: 24px 16px; }
            .header h1 { font-size: 2rem; }
            .services { grid-template-columns: 1fr; }
        }
        @media (max-width: 576px) {
            .floating-phone {
                bottom: 20px;
                left: 20px;
                padding: 12px 18px;
                font-size: 0.9rem;
                gap: 10px;
            }
            .floating-phone i { font-size: 1.4rem; }
            .floating-phone .phone-text { display: none; }
            .floating-phone .phone-number { font-size: 0.85rem; padding: 2px 10px; }
            .back-to-top {
                bottom: 90px;
                right: 20px;
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
            }
        }
        @media (max-width: 480px) {
            .header h1 { font-size: 1.6rem; }
            .contact-row { flex-direction: column; }
            .contact-row .btn { justify-content: center; }
        }
    </style>
</head>
<body>

<!-- ===== دکمه تماس شناور ===== -->
<a href="tel:09933493049" class="floating-phone" aria-label="تماس با ما">
    <i class="fas fa-phone-alt"></i>
    <span class="phone-text">تماس با ما</span>
    <span class="phone-number">0993-349-3049</span>
</a>

<!-- ===== دکمه بازگشت به بالا ===== -->
<button class="back-to-top" id="backToTop" aria-label="بازگشت به بالا">
    <i class="fas fa-chevron-up"></i>
</button>

<div class="container">

    <div class="header">
        <span class="city-icon">🏙️</span>
        <h1>خدمات تعمیرات در <span class="highlight"><?= htmlspecialchars($cityName) ?></span></h1>
        <p>🔧 تعمیرات تخصصی لوازم خانگی با بهترین کیفیت و ضمانت</p>
        <span class="badge"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($cityName) ?> | الو تعمیراتچی</span>
    </div>

    <div class="services">
        
        <a href="/refrigerator-repair-in-<?= $citySlug ?>" class="service-item">
            <span class="icon">🧊</span>
            <h3>تعمیرات یخچال</h3>
            <p>تعمیرات تخصصی انواع یخچال در <?= htmlspecialchars($cityName) ?></p>
            <span class="btn-small">ثبت درخواست</span>
        </a>

        <a href="/washing-machine-repair-in-<?= $citySlug ?>" class="service-item">
            <span class="icon">👕</span>
            <h3>تعمیرات لباسشویی</h3>
            <p>تعمیرات تخصصی انواع لباسشویی در <?= htmlspecialchars($cityName) ?></p>
            <span class="btn-small">ثبت درخواست</span>
        </a>

        <a href="/air-conditioner-repair-in-<?= $citySlug ?>" class="service-item">
            <span class="icon">❄️</span>
            <h3>تعمیرات کولر گازی</h3>
            <p>تعمیرات تخصصی انواع کولر گازی در <?= htmlspecialchars($cityName) ?></p>
            <span class="btn-small">ثبت درخواست</span>
        </a>

        <a href="/tv-repair-in-<?= $citySlug ?>" class="service-item">
            <span class="icon">📺</span>
            <h3>تعمیرات تلویزیون</h3>
            <p>تعمیرات تخصصی انواع تلویزیون در <?= htmlspecialchars($cityName) ?></p>
            <span class="btn-small">ثبت درخواست</span>
        </a>

        <a href="/oven-repair-in-<?= $citySlug ?>" class="service-item">
            <span class="icon">🔥</span>
            <h3>تعمیرات اجاق گاز</h3>
            <p>تعمیرات تخصصی انواع اجاق گاز در <?= htmlspecialchars($cityName) ?></p>
            <span class="btn-small">ثبت درخواست</span>
        </a>

        <a href="/dishwasher-repair-in-<?= $citySlug ?>" class="service-item">
            <span class="icon">🍽️</span>
            <h3>تعمیرات ظرفشویی</h3>
            <p>تعمیرات تخصصی انواع ظرفشویی در <?= htmlspecialchars($cityName) ?></p>
            <span class="btn-small">ثبت درخواست</span>
        </a>

    </div>

    <div class="contact-row">
        <a href="tel:09933493049" class="btn btn-phone">
            <i class="fas fa-phone-alt"></i> تماس بگیرید
        </a>
        <a href="https://wa.me/989933493049" target="_blank" class="btn btn-whatsapp">
            <i class="fab fa-whatsapp"></i> واتساپ
        </a>
    </div>

    <a href="/cities" class="back-link">
        <i class="fas fa-arrow-right"></i> بازگشت به لیست شهرها
    </a>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopBtn = document.getElementById('backToTop');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>

</body>
</html>