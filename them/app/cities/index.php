<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست شهرهای تحت پوشش</title>
    <style>
        body { font-family: Tahoma; padding: 20px; background: #f0f0f0; direction: rtl; }
        .box { max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 16px; }
        h1 { text-align: center; color: #0f172a; border-bottom: 3px solid #facc15; padding-bottom: 15px; }
        .city { display: inline-block; background: #f8fafc; border: 2px solid #e9edf2; padding: 15px 25px; margin: 8px; border-radius: 12px; }
        .city a { text-decoration: none; color: #1a1a2e; font-weight: 700; }
        .city a:hover { color: #facc15; }
    </style>
</head>
<body>
<div class="box">
    <h1>🏙️ شهرهای تحت پوشش</h1>
    <div style="text-align:center;">
        <?php if (!empty($cities)): ?>
            <?php foreach ($cities as $city): ?>
                <div class="city">
                    <a href="/city/<?= $city['slug'] ?>">🏙️ <?= $city['name'] ?></a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;color:#999;">هیچ شهری یافت نشد</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>