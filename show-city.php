<?php
// ============================================================
// اتصال به دیتابیس
// ============================================================
$dbConfig = require __DIR__ . '/config/database.php';

$db = new mysqli(
    $dbConfig['host'],
    $dbConfig['username'],
    $dbConfig['password'],
    $dbConfig['name']
);
unset($dbConfig);
if ($db->connect_error) {
    die("❌ خطا: " . $db->connect_error);
}

// ===== گرفتن نام شهر از آدرس =====
$citySlug = isset($_GET['city']) ? $_GET['city'] : '';

// اگر خالی بود، از آدرس بگیر
if (empty($citySlug)) {
    $path = $_SERVER['REQUEST_URI'];
    $parts = explode('/', trim($path, '/'));
    $citySlug = end($parts);
}

// ===== دریافت اطلاعات شهر =====
$city = null;
if (!empty($citySlug)) {
    $stmt = $db->prepare("SELECT * FROM cities WHERE slug = ? AND status = 1");
    $stmt->bind_param("s", $citySlug);
    $stmt->execute();
    $result = $stmt->get_result();
    $city = $result->fetch_assoc();
}

// ===== اگر شهر پیدا نشد =====
if (!$city) {
    // شهر پیش‌فرض برای تست
    $city = [
        'name' => 'تهران',
        'slug' => 'tehran'
    ];
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>خدمات تعمیرات در <?= htmlspecialchars($city['name']) ?> | الو تعمیراتچی</title>
    <meta name="description" content="خدمات تعمیرات تخصصی در شهر <?= htmlspecialchars($city['name']) ?> - یخچال، لباسشویی، کولر گازی، تلویزیون، اجاق گاز، ظرفشویی" />
    <link rel="canonical" href="https://alotamiratchi.ir/show-city/<?= $city['slug'] ?>" />
    
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
        .header h1 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #0f172a;
        }
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
        }
        .service-item:hover {
            border-color: #facc15;
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(250,204,21,0.1);
        }
        .service-item .icon { font-size: 3rem; display: block; margin-bottom: 8px; }
        .service-item h3 { font-size: 1.2rem; font-weight: 800; color: #0f172a; }
        .service-item p { font-size: 0.9rem; color: #64748b; margin-top: 4px; }
        
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
        
        @media (max-width: 768px) {
            .container { padding: 24px 16px; }
            .header h1 { font-size: 2rem; }
            .services { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .header h1 { font-size: 1.6rem; }
            .contact-row { flex-direction: column; }
            .contact-row .btn { justify-content: center; }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1>خدمات تعمیرات در <span class="highlight"><?= htmlspecialchars($city['name']) ?></span></h1>
        <p>🔧 تعمیرات تخصصی لوازم خانگی با بهترین کیفیت</p>
        <span class="badge"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($city['name']) ?> | الو تعمیراتچی</span>
    </div>

<div class="services">
    
    <!-- ===== یخچال ===== -->
    <a href="/refrigerator-repair-in-<?= $city['slug'] ?>" class="service-item" style="text-decoration: none; color: inherit; display: block;">
        <span class="icon">🧊</span>
        <h3>تعمیرات یخچال</h3>
        <p>تعمیرات تخصصی انواع یخچال در <?= htmlspecialchars($city['name']) ?></p>
        <span style="display: inline-block; margin-top: 10px; padding: 4px 16px; background: #facc15; color: #0f172a; border-radius: 30px; font-size: 0.7rem; font-weight: 700;">ثبت درخواست</span>
    </a>

    <!-- ===== لباسشویی ===== -->
    <a href="/washing-machine-repair-in-<?= $city['slug'] ?>" class="service-item" style="text-decoration: none; color: inherit; display: block;">
        <span class="icon">👕</span>
        <h3>تعمیرات لباسشویی</h3>
        <p>تعمیرات تخصصی انواع لباسشویی در <?= htmlspecialchars($city['name']) ?></p>
        <span style="display: inline-block; margin-top: 10px; padding: 4px 16px; background: #facc15; color: #0f172a; border-radius: 30px; font-size: 0.7rem; font-weight: 700;">ثبت درخواست</span>
    </a>

    <!-- ===== کولر گازی ===== -->
    <a href="/air-conditioner-repair-in-<?= $city['slug'] ?>" class="service-item" style="text-decoration: none; color: inherit; display: block;">
        <span class="icon">❄️</span>
        <h3>تعمیرات کولر گازی</h3>
        <p>تعمیرات تخصصی انواع کولر گازی در <?= htmlspecialchars($city['name']) ?></p>
        <span style="display: inline-block; margin-top: 10px; padding: 4px 16px; background: #facc15; color: #0f172a; border-radius: 30px; font-size: 0.7rem; font-weight: 700;">ثبت درخواست</span>
    </a>

    <!-- ===== تلویزیون ===== -->
    <a href="/tv-repair-in-<?= $city['slug'] ?>" class="service-item" style="text-decoration: none; color: inherit; display: block;">
        <span class="icon">📺</span>
        <h3>تعمیرات تلویزیون</h3>
        <p>تعمیرات تخصصی انواع تلویزیون در <?= htmlspecialchars($city['name']) ?></p>
        <span style="display: inline-block; margin-top: 10px; padding: 4px 16px; background: #facc15; color: #0f172a; border-radius: 30px; font-size: 0.7rem; font-weight: 700;">ثبت درخواست</span>
    </a>

    <!-- ===== اجاق گاز ===== -->
    <a href="/oven-repair-in-<?= $city['slug'] ?>" class="service-item" style="text-decoration: none; color: inherit; display: block;">
        <span class="icon">🔥</span>
        <h3>تعمیرات اجاق گاز</h3>
        <p>تعمیرات تخصصی انواع اجاق گاز در <?= htmlspecialchars($city['name']) ?></p>
        <span style="display: inline-block; margin-top: 10px; padding: 4px 16px; background: #facc15; color: #0f172a; border-radius: 30px; font-size: 0.7rem; font-weight: 700;">ثبت درخواست</span>
    </a>

    <!-- ===== ظرفشویی ===== -->
    <a href="/dishwasher-repair-in-<?= $city['slug'] ?>" class="service-item" style="text-decoration: none; color: inherit; display: block;">
        <span class="icon">🍽️</span>
        <h3>تعمیرات ظرفشویی</h3>
        <p>تعمیرات تخصصی انواع ظرفشویی در <?= htmlspecialchars($city['name']) ?></p>
        <span style="display: inline-block; margin-top: 10px; padding: 4px 16px; background: #facc15; color: #0f172a; border-radius: 30px; font-size: 0.7rem; font-weight: 700;">ثبت درخواست</span>
    </a>

</div>

    <div class="contact-row">
        <a href="tel:09123456789" class="btn btn-phone"><i class="fas fa-phone-alt"></i> تماس بگیرید</a>
        <a href="https://wa.me/989123456789" target="_blank" class="btn btn-whatsapp"><i class="fab fa-whatsapp"></i> واتساپ</a>
    </div>

    <a href="/cities" class="back-link"><i class="fas fa-arrow-right"></i> بازگشت به لیست شهرها</a>

</div>

</body>
</html>
<?php
if (isset($stmt)) $stmt->close();
$db->close();
?>