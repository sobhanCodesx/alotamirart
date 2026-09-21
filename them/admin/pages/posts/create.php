<?php
// ============================================================
// سیستم تحلیل سئو و خوانایی (مشابه Yoast & Rank Math)
// ============================================================

// ===== تابع اصلی تحلیل =====
function analyzeSEO($content, $keyword = '', $title = '', $description = '') {
    $result = [
        'score' => 0,
        'details' => [],
        'suggestions' => [],
        'status' => 'red', // red, yellow, green
        'readability' => 0
    ];

    // ===== ۱. تحلیل سئو =====
    $seoScore = 0;
    $seoTotal = 8; // تعداد معیارهای سئو

    // 1.1. وجود کلمه کلیدی در عنوان
    if (!empty($keyword) && !empty($title)) {
        if (stripos($title, $keyword) !== false) {
            $seoScore++;
            $result['details']['title_keyword'] = '✅ کلمه کلیدی در عنوان وجود دارد.';
        } else {
            $result['suggestions'][] = '⚠️ کلمه کلیدی را به عنوان اضافه کنید.';
            $result['details']['title_keyword'] = '❌ کلمه کلیدی در عنوان وجود ندارد.';
        }
    }

    // 1.2. وجود کلمه کلیدی در توضیحات متا
    if (!empty($keyword) && !empty($description)) {
        if (stripos($description, $keyword) !== false) {
            $seoScore++;
            $result['details']['meta_keyword'] = '✅ کلمه کلیدی در توضیحات متا وجود دارد.';
        } else {
            $result['suggestions'][] = '⚠️ کلمه کلیدی را به توضیحات متا اضافه کنید.';
            $result['details']['meta_keyword'] = '❌ کلمه کلیدی در توضیحات متا وجود ندارد.';
        }
    }

    // 1.3. وجود کلمه کلیدی در H1 (اولین تگ h1)
    preg_match('/<h1[^>]*>(.*?)<\/h1>/i', $content, $h1Match);
    if (!empty($h1Match[1]) && !empty($keyword)) {
        if (stripos($h1Match[1], $keyword) !== false) {
            $seoScore++;
            $result['details']['h1_keyword'] = '✅ کلمه کلیدی در تگ H1 وجود دارد.';
        } else {
            $result['suggestions'][] = '⚠️ کلمه کلیدی را در تگ H1 (عنوان اصلی) قرار دهید.';
            $result['details']['h1_keyword'] = '❌ کلمه کلیدی در تگ H1 وجود ندارد.';
        }
    } else {
        $result['suggestions'][] = '⚠️ از تگ H1 برای عنوان اصلی استفاده کنید.';
        $result['details']['h1_keyword'] = '❌ تگ H1 پیدا نشد!';
    }

    // 1.4. چگالی کلمه کلیدی (2% تا 3% ایده‌آل)
    if (!empty($keyword)) {
        $text = strip_tags($content);
        $wordCount = str_word_count($text, 0, 'آابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهی');
        $keywordCount = substr_count($text, $keyword);
        $density = ($wordCount > 0) ? ($keywordCount / $wordCount) * 100 : 0;
        
        if ($density >= 1.5 && $density <= 3.5) {
            $seoScore++;
            $result['details']['keyword_density'] = '✅ چگالی کلمه کلیدی: ' . round($density, 1) . '% (مناسب)';
        } elseif ($density < 1.5) {
            $result['suggestions'][] = '⚠️ چگالی کلمه کلیدی کم است (' . round($density, 1) . '%). بیشتر استفاده کنید.';
            $result['details']['keyword_density'] = '❌ چگالی کلمه کلیدی: ' . round($density, 1) . '% (کم)';
        } else {
            $result['suggestions'][] = '⚠️ چگالی کلمه کلیدی زیاد است (' . round($density, 1) . '%). کمتر استفاده کنید.';
            $result['details']['keyword_density'] = '❌ چگالی کلمه کلیدی: ' . round($density, 1) . '% (زیاد)';
        }
    }

    // 1.5. وجود تگ‌های H2/H3
    preg_match_all('/<h[2-6][^>]*>/i', $content, $headings);
    $headingCount = count($headings[0]);
    if ($headingCount >= 2) {
        $seoScore++;
        $result['details']['headings'] = '✅ تعداد زیرعنوان‌ها: ' . $headingCount . ' (مناسب)';
    } else {
        $result['suggestions'][] = '⚠️ از زیرعنوان‌های H2 و H3 برای بخش‌بندی متن استفاده کنید.';
        $result['details']['headings'] = '❌ تعداد زیرعنوان‌ها: ' . $headingCount . ' (کم است)';
    }

    // 1.6. وجود لینک داخلی
    preg_match_all('/<a[^>]*href=["\'](?!https?:\/\/)[^"\']*["\'][^>]*>/i', $content, $internalLinks);
    if (count($internalLinks[0]) >= 1) {
        $seoScore++;
        $result['details']['internal_links'] = '✅ لینک داخلی وجود دارد.';
    } else {
        $result['suggestions'][] = '⚠️ به یک مقاله دیگر از سایت خود لینک دهید (لینک داخلی).';
        $result['details']['internal_links'] = '❌ هیچ لینک داخلی یافت نشد.';
    }

    // 1.7. وجود تگ Alt برای تصاویر
    preg_match_all('/<img[^>]*alt=["\']([^"\']*)["\'][^>]*>/i', $content, $imgAlt);
    $emptyAlt = 0;
    foreach ($imgAlt[1] as $alt) {
        if (trim($alt) == '') $emptyAlt++;
    }
    if (count($imgAlt[0]) > 0 && $emptyAlt == 0) {
        $seoScore++;
        $result['details']['image_alt'] = '✅ همه تصاویر دارای توضیح Alt هستند.';
    } elseif (count($imgAlt[0]) > 0 && $emptyAlt > 0) {
        $result['suggestions'][] = '⚠️ برای ' . $emptyAlt . ' تصویر توضیح Alt وارد کنید.';
        $result['details']['image_alt'] = '❌ ' . $emptyAlt . ' تصویر بدون توضیح Alt هستند.';
    } else {
        $result['details']['image_alt'] = 'ℹ️ تصویری در مقاله وجود ندارد.';
    }

    // 1.8. طول متن (حداقل 300 کلمه)
    $text = strip_tags($content);
    $wordCount = str_word_count($text, 0, 'آابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهی');
    if ($wordCount >= 300) {
        $seoScore++;
        $result['details']['word_count'] = '✅ تعداد کلمات: ' . $wordCount . ' (مناسب)';
    } else {
        $result['suggestions'][] = '⚠️ تعداد کلمات مقاله کم است (' . $wordCount . ' کلمه). حداقل 300 کلمه بنویسید.';
        $result['details']['word_count'] = '❌ تعداد کلمات: ' . $wordCount . ' (کم است)';
    }

    // ===== نمره نهایی سئو =====
    $seoScore = ($seoScore / $seoTotal) * 100;

    // ===== ۲. تحلیل خوانایی =====
    $readabilityScore = 0;
    $readabilityTotal = 4;

    // 2.1. طول جملات (میانگین کمتر از 20 کلمه)
    $sentences = preg_split('/[.!?]+/', $text);
    $sentenceCount = count($sentences);
    $wordsPerSentence = ($sentenceCount > 0) ? ($wordCount / $sentenceCount) : 0;
    if ($wordsPerSentence <= 20) {
        $readabilityScore++;
        $result['details']['sentence_length'] = '✅ میانگین طول جملات: ' . round($wordsPerSentence, 1) . ' کلمه (مناسب)';
    } else {
        $result['suggestions'][] = '⚠️ جملات را کوتاه‌تر بنویسید (میانگین: ' . round($wordsPerSentence, 1) . ' کلمه).';
        $result['details']['sentence_length'] = '❌ میانگین طول جملات: ' . round($wordsPerSentence, 1) . ' کلمه (زیاد است)';
    }

    // 2.2. طول پاراگراف‌ها (حداکثر 4 خط)
    $paragraphs = explode("\n", $text);
    $longParagraphs = 0;
    foreach ($paragraphs as $p) {
        if (str_word_count($p, 0, 'آابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهی') > 100) {
            $longParagraphs++;
        }
    }
    if ($longParagraphs == 0) {
        $readabilityScore++;
        $result['details']['paragraph_length'] = '✅ پاراگراف‌ها کوتاه و مناسب هستند.';
    } else {
        $result['suggestions'][] = '⚠️ ' . $longParagraphs . ' پاراگراف طولانی دارید. آنها را کوتاه‌تر کنید.';
        $result['details']['paragraph_length'] = '❌ ' . $longParagraphs . ' پاراگراف طولانی وجود دارد.';
    }

    // 2.3. استفاده از کلمات انتقالی
    $transitionWords = ['اما', 'با این حال', 'علاوه بر این', 'بنابراین', 'برای مثال', 'به همین دلیل', 'در نتیجه', 'از سوی دیگر'];
    $transitionCount = 0;
    foreach ($transitionWords as $word) {
        if (stripos($text, $word) !== false) $transitionCount++;
    }
    if ($transitionCount >= 3) {
        $readabilityScore++;
        $result['details']['transition_words'] = '✅ استفاده از کلمات انتقالی: ' . $transitionCount . ' (مناسب)';
    } else {
        $result['suggestions'][] = '⚠️ از کلمات انتقالی بیشتری استفاده کنید (مثل: اما، بنابراین، علاوه بر این).';
        $result['details']['transition_words'] = '❌ کلمات انتقالی: ' . $transitionCount . ' (کم است)';
    }

    // 2.4. استفاده از لیست‌ها
    if (preg_match('/<(ul|ol)/i', $content)) {
        $readabilityScore++;
        $result['details']['lists'] = '✅ از لیست‌های (ul/ol) استفاده کرده‌اید.';
    } else {
        $result['suggestions'][] = '⚠️ از لیست‌ها (نقطه‌ای یا شماره‌دار) برای بخش‌بندی بهتر استفاده کنید.';
        $result['details']['lists'] = '❌ هیچ لیستی یافت نشد.';
    }

    // ===== نمره نهایی خوانایی =====
    $readabilityScore = ($readabilityScore / $readabilityTotal) * 100;

    // ===== نمره کلی =====
    $totalScore = ($seoScore + $readabilityScore) / 2;

    // ===== تعیین وضعیت =====
    if ($totalScore >= 80) {
        $status = 'green';
        $statusText = '🟢 عالی!';
    } elseif ($totalScore >= 50) {
        $status = 'yellow';
        $statusText = '🟡 نیاز به بهبود';
    } else {
        $status = 'red';
        $statusText = '🔴 نیاز به بازنویسی';
    }

    // ===== نتیجه نهایی =====
    return [
        'seo_score' => round($seoScore, 1),
        'readability_score' => round($readabilityScore, 1),
        'total_score' => round($totalScore, 1),
        'status' => $status,
        'status_text' => $statusText,
        'details' => $result['details'],
        'suggestions' => $result['suggestions'],
        'word_count' => $wordCount,
        'keyword_density' => isset($density) ? round($density, 1) : 0
    ];
}

// ============================================================
// نمونه استفاده در صفحه ایجاد/ویرایش پست
// ============================================================

// دریافت محتوا (اگر فرم ارسال شده)
$content = isset($_POST['content']) ? $_POST['content'] : '';
$title = isset($_POST['title']) ? $_POST['title'] : '';
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : ''; // کلمه کلیدی اصلی
$description = isset($_POST['description']) ? $_POST['description'] : '';

$seoResult = null;
if (!empty($content)) {
    $seoResult = analyzeSEO($content, $keyword, $title, $description);
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="ROBOTS" content="noindex,nofollow">
    <title>پنل مدیریت | افزودن پست + تحلیل سئو</title>
    <script src="//cdn.ckeditor.com/4.25.1-lts/full/ckeditor.js"></script>
    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>

    <style>
        /* استایل طلایی-آبی */
        body { background: #0a0e27 !important; }
        .content-wrapper {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1a3e 50%, #0d1b2a 100%);
            padding: 30px 0;
            min-height: 100vh;
        }
        .container { max-width: 1100px; }
        
        .form-container {
            background: rgba(10, 14, 39, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid #FFD700;
            border-radius: 15px;
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.2);
            padding: 30px;
        }
        
        .page-title {
            color: #FFD700;
            text-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
            font-weight: 800;
            border-bottom: 3px solid #FFD700;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        
        .form-label {
            color: #FFD700;
            font-weight: 700;
            display: block;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .form-control-custom {
            background: rgba(10, 14, 39, 0.8);
            color: #E0E0E0;
            border: 2px solid #FFD700;
            border-radius: 10px;
            padding: 12px 18px;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        
        .form-control-custom:focus {
            border-color: #4A90D9;
            box-shadow: 0 0 50px rgba(74, 144, 217, 0.2);
            outline: none;
        }
        
        .form-control-custom::placeholder { color: #666; }
        
        /* ===== باکس نتایج سئو ===== */
        .seo-box {
            background: rgba(10, 14, 39, 0.8);
            border: 2px solid #FFD700;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            animation: fadeIn 0.5s ease;
        }
        
        .seo-score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 900;
            margin: 0 auto 15px;
            border: 5px solid;
        }
        
        .seo-score-green { border-color: #00c853; color: #00c853; background: rgba(0, 200, 83, 0.1); }
        .seo-score-yellow { border-color: #ffc107; color: #ffc107; background: rgba(255, 193, 7, 0.1); }
        .seo-score-red { border-color: #ff1744; color: #ff1744; background: rgba(255, 23, 68, 0.1); }
        
        .seo-detail-item {
            padding: 8px 12px;
            border-bottom: 1px solid rgba(255, 215, 0, 0.1);
            font-size: 14px;
            color: #E0E0E0;
        }
        
        .seo-detail-item:last-child { border-bottom: none; }
        
        .seo-suggestion {
            background: rgba(255, 215, 0, 0.05);
            border-right: 3px solid #FFD700;
            padding: 8px 15px;
            margin: 5px 0;
            color: #FFD700;
            font-size: 13px;
            border-radius: 0 8px 8px 0;
        }
        
        .seo-status-green { background: rgba(0, 200, 83, 0.15); border-color: #00c853; color: #00c853; }
        .seo-status-yellow { background: rgba(255, 193, 7, 0.15); border-color: #ffc107; color: #ffc107; }
        .seo-status-red { background: rgba(255, 23, 68, 0.15); border-color: #ff1744; color: #ff1744; }
        
        .btn-submit {
            background: linear-gradient(135deg, #4A90D9, #2E6BB0);
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 700;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 0 50px rgba(74, 144, 217, 0.5);
        }
        
        .cke { border: 2px solid #FFD700 !important; border-radius: 10px !important; }
        .cke_top { background: rgba(10, 14, 39, 0.9) !important; border-bottom: 1px solid #FFD700 !important; }
        .cke_contents { background: rgba(10, 14, 39, 0.8) !important; color: #E0E0E0 !important; min-height: 350px !important; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .row { display: flex; gap: 20px; flex-wrap: wrap; }
        .col-6 { flex: 1; min-width: 300px; }
        
        @media (max-width: 768px) {
            .form-container { padding: 15px; }
            .col-6 { min-width: 100%; }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
    </div>
    
    <div class="content-wrapper">
        <div class="container">
            <div class="form-container">
                
                <h1 class="page-title">🚀 افزودن پست جدید + تحلیل سئو</h1>

                <!-- ===== فرم ===== -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">عنوان مقاله</label>
                            <input type="text" name="title" class="form-control-custom" placeholder="عنوان را وارد کنید..." value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                            
                            <label class="form-label">کلمه کلیدی اصلی</label>
                            <input type="text" name="keyword" class="form-control-custom" placeholder="کلمه کلیدی اصلی..." value="<?= isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : '' ?>">
                            
                            <label class="form-label">توضیحات متا (Meta Description)</label>
                            <input type="text" name="description" class="form-control-custom" placeholder="توضیحات برای گوگل..." value="<?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?>">
                            
                            <label class="form-label">تصویر شاخص</label>
                            <input type="file" name="img" class="form-control-custom" accept="image/*">
                        </div>
                        
                        <div class="col-6">
                            <label class="form-label">دسته‌بندی</label>
                            <select name="post_id" class="form-control-custom">
                                <option value="">انتخاب دسته...</option>
                                <?php foreach ($menus as $m) { ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['title'] ?></option>
                                <?php } ?>
                            </select>
                            
                            <label class="form-label">تگ‌ها</label>
                            <input type="text" name="tags" class="form-control-custom" placeholder="تگ‌ها با کاما جدا کنید..." value="<?= isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : '' ?>">
                            
                            <label class="form-label">شماره تماس</label>
                            <input type="text" name="contact_number" class="form-control-custom" placeholder="0912..." value="<?= isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : '' ?>">
                        </div>
                    </div>
                    
                    <label class="form-label">متن اصلی مقاله</label>
                    <textarea name="content" id="editor1"><?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' ?></textarea>
                    
                    <button type="submit" class="btn-submit">📊 تحلیل سئو + ذخیره</button>
                </form>

                <!-- ===== نمایش نتایج تحلیل ===== -->
                <?php if ($seoResult): ?>
                <div class="seo-box">
                    <h3 style="color: #FFD700; text-align: center; margin-bottom: 20px;">📈 نتیجه تحلیل سئو و خوانایی</h3>
                    
                    <div class="row" style="text-align: center; margin-bottom: 20px;">
                        <div class="col-6">
                            <div class="seo-score-circle <?= $seoResult['seo_score'] >= 70 ? 'seo-score-green' : ($seoResult['seo_score'] >= 50 ? 'seo-score-yellow' : 'seo-score-red') ?>">
                                <?= $seoResult['seo_score'] ?>%
                            </div>
                            <div style="color: #FFD700; font-weight: 700;">سئو (SEO)</div>
                        </div>
                        <div class="col-6">
                            <div class="seo-score-circle <?= $seoResult['readability_score'] >= 70 ? 'seo-score-green' : ($seoResult['readability_score'] >= 50 ? 'seo-score-yellow' : 'seo-score-red') ?>">
                                <?= $seoResult['readability_score'] ?>%
                            </div>
                            <div style="color: #FFD700; font-weight: 700;">خوانایی (Readability)</div>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-bottom: 20px;">
                        <span style="font-size: 18px; font-weight: 700; color: <?= $seoResult['status'] == 'green' ? '#00c853' : ($seoResult['status'] == 'yellow' ? '#ffc107' : '#ff1744') ?>;">
                            <?= $seoResult['status_text'] ?>
                        </span>
                        <span style="color: #E0E0E0; margin-right: 20px;">
                            🖊️ تعداد کلمات: <?= $seoResult['word_count'] ?>
                        </span>
                        <span style="color: #E0E0E0; margin-right: 20px;">
                            📊 چگالی: <?= $seoResult['keyword_density'] ?>%
                        </span>
                        <span style="color: #E0E0E0; margin-right: 20px;">
                            🎯 نمره کلی: <?= $seoResult['total_score'] ?>%
                        </span>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <h4 style="color: #FFD700;">📋 جزئیات سئو</h4>
                            <?php foreach ($seoResult['details'] as $key => $detail): ?>
                                <div class="seo-detail-item"><?= $detail ?></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-6">
                            <h4 style="color: #FFD700;">💡 پیشنهادات بهبود</h4>
                            <?php if (empty($seoResult['suggestions'])): ?>
                                <div class="seo-suggestion" style="border-color: #00c853; color: #00c853;">
                                    ✅ عالی! همه موارد رعایت شده است.
                                </div>
                            <?php else: ?>
                                <?php foreach ($seoResult['suggestions'] as $suggestion): ?>
                                    <div class="seo-suggestion"><?= $suggestion ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script>
        CKEDITOR.replace('editor1', {
            language: 'fa',
            contentsLangDirection: 'rtl',
            height: 500,
            filebrowserUploadUrl: '<?= assets('admin/upload-image.php') ?>',
            filebrowserImageUploadUrl: '<?= assets('admin/upload-image.php') ?>',
            uploadUrl: '<?= assets('admin/upload-image.php') ?>',
            toolbar: [
                { name: 'document', items: ['Source', 'Preview'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'Undo', 'Redo'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'] },
                { name: 'align', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                { name: 'lists', items: ['NumberedList', 'BulletedList'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'] },
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize'] }
            ]
        });
    </script>

</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>
</html>