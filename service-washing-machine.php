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

// ===== گرفتن اطلاعات از آدرس =====
$citySlug = isset($_GET['city']) ? $_GET['city'] : 'tehran';

// ===== دریافت اطلاعات شهر =====
$city = null;
if (!empty($citySlug)) {
    $stmt = $db->prepare("SELECT * FROM cities WHERE slug = ? AND status = 1");
    $stmt->bind_param("s", $citySlug);
    $stmt->execute();
    $city = $stmt->get_result()->fetch_assoc();
}

if (!$city) {
    $city = ['name' => 'تهران', 'slug' => 'tehran'];
}

// ============================================================
// ===== دریافت برندهای لباسشویی از دیتابیس =====
// ============================================================
$brands = [];
try {
    $stmt = $db->prepare("
        SELECT b.* FROM brands b
        INNER JOIN brand_service bs ON b.id = bs.brand_id
        WHERE bs.service_id = 2 AND b.status = 1
        ORDER BY b.name ASC
    ");
    $stmt->execute();
    $brandsResult = $stmt->get_result();
    $brands = $brandsResult->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $brands = [];
}

// ===== تنظیمات سرویس =====
$serviceName = 'لباسشویی';
$serviceIcon = '👕';
$serviceTitle = 'بهترین تعمیرگاه لباسشویی';
$serviceDesc = 'تعمیرات تخصصی انواع ماشین لباسشویی با بهترین کیفیت و ضمانت';
$serviceSlug = 'washing-machine';

// ===== تنظیمات سئو =====
$pageTitle = "{$serviceTitle} در {$city['name']} | الو تعمیراتچی";
$pageDescription = "تعمیرات تخصصی {$serviceName} در {$city['name']} با بهترین کیفیت و ضمانت. تعمیر انواع {$serviceName} در {$city['name']} توسط تکنسین‌های مجرب.";
$canonical = "https://alotamiratchi.ir/{$serviceSlug}-repair-in-{$city['slug']}";
$ogImage = "https://alotamiratchi.ir/assets/images/logo.png";
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>" />
    <link rel="canonical" href="<?= $canonical ?>" />
    
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
        .container { max-width: 1000px; margin: 0 auto; background: #fff; border-radius: 30px; padding: 40px 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.06); }
        .header { text-align: center; padding: 20px 0 30px; border-bottom: 4px solid #facc15; margin-bottom: 30px; }
        .header .icon-big { font-size: 4rem; display: block; margin-bottom: 10px; }
        .header h1 { font-size: 2.5rem; font-weight: 900; color: #0f172a; }
        .header h1 .highlight { color: #facc15; }
        .header p { font-size: 1.1rem; color: #64748b; margin-top: 6px; }
        .header .badge { display: inline-block; background: #f1f5f9; padding: 4px 20px; border-radius: 30px; font-size: 0.8rem; color: #475569; margin-top: 10px; }
        
        .info-box { background: #f8fafc; border-radius: 20px; padding: 30px; margin: 20px 0; border: 2px solid #e9edf2; }
        .info-box h2 { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; }
        .info-box h2 i { color: #facc15; margin-left: 10px; }
        .info-box p { font-size: 1rem; line-height: 2; color: #475569; }
        
        .service-list { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 16px 0; list-style: none; padding: 0; }
        .service-list li { background: #fff; padding: 10px 16px; border-radius: 40px; font-size: 0.9rem; font-weight: 600; color: #0f172a; border: 2px solid #e9edf2; text-align: center; transition: 0.3s; }
        .service-list li:hover { border-color: #facc15; background: #fef9e7; }
        .service-list li i { color: #f59e0b; margin-left: 8px; }
        
        .brands-section { margin: 30px 0 25px; padding: 30px 25px; background: linear-gradient(135deg, #f8fafc, #e9edf2); border-radius: 24px; border: 2px solid #e9edf2; box-shadow: 0 8px 30px rgba(0,0,0,0.04); }
        .brands-section .title { text-align: center; margin-bottom: 24px; }
        .brands-section .title .big-icon { font-size: 2.5rem; display: block; margin-bottom: 6px; }
        .brands-section .title h3 { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0; }
        .brands-section .title h3 span { color: #facc15; }
        .brands-section .title p { color: #64748b; font-size: 0.95rem; margin-top: 4px; }
        .brands-section .title .line { width: 60px; height: 3px; background: linear-gradient(90deg, #facc15, #f59e0b); margin: 10px auto 0; border-radius: 2px; }
        .brands-section .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; }
        .brands-section .brand-item { background: #ffffff; padding: 14px 10px; border-radius: 12px; text-align: center; border: 2px solid #e9edf2; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .brands-section .brand-item:hover { border-color: #facc15; transform: translateY(-3px); box-shadow: 0 12px 30px rgba(250, 204, 21, 0.15); }
        .brands-section .brand-item .brand-icon { font-size: 1.8rem; display: block; }
        .brands-section .brand-item .brand-name { font-size: 0.9rem; font-weight: 700; color: #0f172a; display: block; margin-top: 6px; }
        
        .contact-row { display: flex; justify-content: center; gap: 16px; margin: 30px 0 20px; flex-wrap: wrap; }
        .contact-row .btn { padding: 14px 36px; border-radius: 60px; font-weight: 700; font-size: 1rem; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; font-family: 'Vazirmatn', sans-serif; transition: 0.3s; }
        .contact-row .btn-phone { background: #0f172a; color: #fff; border: 2px solid #0f172a; }
        .contact-row .btn-phone:hover { background: #facc15; color: #0f172a; border-color: #facc15; }
        .contact-row .btn-whatsapp { background: #25D366; color: #fff; border: 2px solid #25D366; }
        .contact-row .btn-whatsapp:hover { background: #1da851; border-color: #1da851; }
        
        .back-link { display: block; text-align: center; color: #94a3b8; text-decoration: none; font-weight: 600; margin-top: 20px; }
        .back-link:hover { color: #facc15; }
        
        .operator-section { background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 20px; padding: 20px 25px; margin: 20px 0; border: 2px solid #facc15; box-shadow: 0 8px 30px rgba(250, 204, 21, 0.08); text-align: center; }
        .operator-section .inner { display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap; }
        .operator-section .icon-box { background: rgba(250, 204, 21, 0.12); border-radius: 50%; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border: 2px solid #facc15; }
        .operator-section .icon-box i { font-size: 1.8rem; color: #facc15; }
        .operator-section .info .label { font-size: 0.75rem; color: #94a3b8; font-weight: 500; letter-spacing: 1px; }
        .operator-section .info .phone { font-size: 1.6rem; font-weight: 900; color: #facc15; direction: ltr; }
        .operator-section .info .phone a { color: #facc15; text-decoration: none; transition: 0.3s; }
        .operator-section .info .phone a:hover { color: #ffffff; }
        .operator-section .info .time { font-size: 0.65rem; color: #64748b; margin-top: 2px; }
        
        @media (max-width: 768px) { .container { padding: 24px 16px; } .header h1 { font-size: 2rem; } .service-list { grid-template-columns: 1fr; } .brands-section .grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); } }
        @media (max-width: 480px) { .header h1 { font-size: 1.6rem; } .contact-row { flex-direction: column; } .contact-row .btn { justify-content: center; } .brands-section .grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); } }
    </style>
</head>
<body>

<div class="container">

    <!-- ===== هدر ===== -->
    <div class="header">
        <span class="icon-big"><?= $serviceIcon ?></span>
        <h1><?= $serviceTitle ?> در <span class="highlight"><?= htmlspecialchars($city['name']) ?></span></h1>
        <p>🔧 <?= $serviceDesc ?> با بهترین کیفیت و ضمانت</p>
        <span class="badge"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($city['name']) ?> | الو تعمیراتچی</span>
    </div>

    <!-- ===== اپراتور خدمات ===== -->
    <div class="operator-section">
        <div class="inner">
            <div class="icon-box">
                <i class="fas fa-headset"></i>
            </div>
            <div class="info">
                <div class="label">📞 اپراتور خدمات</div>
                <div class="phone">
                    <a href="tel:09933493049" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#facc15'">0993-349-3049</a>
                </div>
                <div class="time"><i class="fas fa-clock"></i> پاسخگویی ۲۴ ساعته</div>
            </div>
        </div>
    </div>

    <!-- ===== محتوای اصلی ===== -->
    <div class="info-box">
        <h2><i class="fas fa-info-circle"></i> <?= $serviceTitle ?> در <?= htmlspecialchars($city['name']) ?></h2>
        <p>
            <strong>الو تعمیراتچی</strong> با تکنسین‌های مجرب و حرفه‌ای، خدمات تعمیرات تخصصی 
            <strong><?= $serviceName ?></strong> را در شهر 
            <strong><?= htmlspecialchars($city['name']) ?></strong> 
            با بهترین کیفیت و ضمانت انجام می‌دهد.
        </p>
        
        <ul class="service-list">
            <li><i class="fas fa-wrench"></i> تعمیرات تخصصی <?= $serviceName ?></li>
            <li><i class="fas fa-wrench"></i> تعمیر برد الکترونیکی</li>
            <li><i class="fas fa-wrench"></i> تعمیر موتور</li>
            <li><i class="fas fa-wrench"></i> تعمیر پمپ تخلیه</li>
            <li><i class="fas fa-wrench"></i> قطعات اصلی و با کیفیت</li>
            <li><i class="fas fa-wrench"></i> ضمانت انجام کار</li>
        </ul>
        
        <p>
            برای دریافت مشاوره رایگان و ثبت درخواست تعمیرات <strong><?= $serviceName ?></strong> 
            در <strong><?= htmlspecialchars($city['name']) ?></strong>، از طریق شماره تماس یا واتساپ با ما در ارتباط باشید.
        </p>
    </div>

    <!-- ========================================================== -->
    <!-- ===== ✅ برندهای لباسشویی (از دیتابیس) ===== -->
    <!-- ========================================================== -->
    <?php if (!empty($brands)): ?>
    <div class="brands-section">
        <div class="title">
            <span class="big-icon">🏷️</span>
            <h3>برندهای تحت پوشش <span><?= htmlspecialchars($city['name']) ?></span></h3>
            <p>تعمیرات تخصصی انواع برندهای <?= $serviceName ?> در <?= htmlspecialchars($city['name']) ?></p>
            <div class="line"></div>
        </div>
        <div class="grid">
            <?php foreach ($brands as $brand): ?>
                <div class="brand-item">
                    <span class="brand-icon">🏷️</span>
                    <span class="brand-name"><?= htmlspecialchars($brand['name']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ========================================================== -->
    <!-- ===== دکمه‌های تماس ===== -->
    <!-- ========================================================== -->
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

</body>
</html>
<?php
if (isset($stmt)) $stmt->close();
$db->close();
?>