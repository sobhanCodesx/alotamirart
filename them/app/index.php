<?php
$siteTitle = isset($dataSeo['title']) ? strip_tags((string)$dataSeo['title']) : 'الو تعمیراتچی';
$siteDescription = 'در الو تعمیراتچی خدمات تعمیر یخچال، لباسشویی، ظرفشویی، کولر گازی و سایر لوازم خانگی را در شهر خود پیدا کنید و با ارائه‌دهندگان خدمات ارتباط بگیرید.';
$homeSeoTitle = 'الو تعمیراتچی | خدمات تعمیر لوازم خانگی در شهر شما';
$homeCanonical = 'https://www.alotamiratchi.ir/';
$heroRaw = isset($dataSeo['header']) ? (string)$dataSeo['header'] : '';
$heroUrl = $heroRaw !== '' && preg_match('#^https?://#i', $heroRaw) ? $heroRaw : assets($heroRaw);
$phone = isset($dataFooter['phon']) ? strip_tags((string)$dataFooter['phon']) : '';
$phoneHref = preg_replace('/[^\d+]/', '', $phone);
$loginMessage = flash('login');
$seoLogo = trim((string)($dataSeo['logo'] ?? 'public/src/img/logo.png'));
$seoLogo = $seoLogo !== '' ? $seoLogo : 'public/src/img/logo.png';
$homePreviewImage = preg_match('~^https?://~i', $seoLogo) ? $seoLogo : assets($seoLogo);
$homeSchema = [
  '@context' => 'https://schema.org',
  '@graph' => [
    ['@type' => 'Organization','@id' => $homeCanonical . '#organization','name' => 'الو تعمیراتچی','url' => $homeCanonical,'logo' => $homePreviewImage],
    ['@type' => 'WebSite','@id' => $homeCanonical . '#website','name' => 'الو تعمیراتچی','url' => $homeCanonical,'inLanguage' => 'fa-IR','publisher' => ['@id' => $homeCanonical . '#organization']],
  ],
];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <title><?= htmlspecialchars($homeSeoTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <?php if ($siteDescription !== ''): ?><meta name="description" content="<?= htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
  <link rel="canonical" href="<?= $homeCanonical ?>">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="fa_IR">
  <meta property="og:site_name" content="الو تعمیراتچی">
  <meta property="og:url" content="<?= $homeCanonical ?>">
  <meta property="og:title" content="<?= htmlspecialchars($homeSeoTitle, ENT_QUOTES, 'UTF-8') ?>">
  <?php if ($siteDescription !== ''): ?><meta property="og:description" content="<?= htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
  <meta property="og:image" content="<?= htmlspecialchars($homePreviewImage, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:image:alt" content="لوگوی الو تعمیراتچی">
  <meta name="twitter:card" content="summary">
  <script type="application/ld+json"><?= json_encode($homeSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
  <?php require BASE_PATH . '/them/app/layout/heading.php'; ?>
</head>
<body>
<?php require BASE_PATH . '/them/app/layout/header.php'; ?>
<main id="main-content">
  <section class="hero"<?= $heroRaw !== '' ? ' style="background-image:url(\'' . htmlspecialchars($heroUrl, ENT_QUOTES, 'UTF-8') . '\')"' : '' ?>>
    <div class="site-container">
      <div class="hero__content">
        <?php if ($loginMessage !== ''): ?><div class="flash-message"><?= htmlspecialchars($loginMessage, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <span class="hero__badge">● تعمیرات تخصصی در شهر شما</span>
        <h1>خدمات تعمیر لوازم خانگی در شهر شما</h1>
        <p class="hero__lead">الو تعمیراتچی راهی برای پیدا کردن مطالب تخصصی و خدمات مرتبط با تعمیر یخچال، لباسشویی، ظرفشویی و سایر لوازم خانگی است. شهر یا برند موردنظرتان را جست‌وجو کنید و راه‌های ارتباط با ارائه‌دهندگان خدمات را ببینید.</p>
        <form method="post" action="<?= assets('search/1') ?>" class="hero-search" role="search">
          <input type="search" name="search" placeholder="نام برند، شهر یا موضوع را جستجو کنید" aria-label="جستجو در سایت" autocomplete="off">
          <button type="submit">جستجو</button>
        </form>
        <div class="hero-trust" aria-label="مزیت‌های خدمات">
          <span>✓ دسترسی سریع به خدمات</span><span>✓ محتوای تخصصی و کاربردی</span><span>✓ پوشش شهرها و برندهای مختلف</span>
        </div>
      </div>
    </div>
  </section>

  <section class="site-section--compact"><div class="site-container"><div class="trust-strip">
    <div class="trust-item"><div class="trust-item__icon">⌂</div><div><strong>خدمات نزدیک شما</strong><span>شهر را انتخاب کنید و سریع‌تر اقدام کنید</span></div></div>
    <div class="trust-item"><div class="trust-item__icon">⚙</div><div><strong>راهنمای تخصصی</strong><span>مطالب کاربردی برای تصمیم‌گیری بهتر</span></div></div>
    <div class="trust-item"><div class="trust-item__icon">◎</div><div><strong>پوشش برندها</strong><span>دسترسی به نمایندگی‌ها و محتوای برند</span></div></div>
    <div class="trust-item"><div class="trust-item__icon">☎</div><div><strong>ارتباط آسان</strong><span>مسیر تماس روشن و بدون پیچیدگی</span></div></div>
  </div></div></section>

  <section class="site-section"><div class="site-container">
    <div class="section-head"><div><span class="site-eyebrow">مجله تعمیرات</span><h2 class="site-title">آخرین مقالات</h2><p class="site-subtitle">راهنماها، نکات نگهداری و پاسخ به سوالات رایج درباره لوازم خانگی.</p></div></div>
    <?php if (!empty($post) && is_array($post)): ?><div class="content-grid">
      <?php foreach ($post as $item): ?><article class="content-card" data-reveal>
        <a class="content-card__link" href="<?= assets('post/' . (int)$item['id']) ?>">
          <div class="content-card__media"><img src="<?= assets($item['img']) ?>" alt="" width="640" height="400" loading="lazy" decoding="async"></div>
          <div class="content-card__body"><span class="content-card__meta">مقاله آموزشی</span><h3><?= htmlspecialchars(clean_display_text(isset($item['title']) ? $item['title'] : ''), ENT_QUOTES, 'UTF-8') ?></h3><span class="content-card__summary"><?= htmlspecialchars(excerpt_text(isset($item['content']) ? $item['content'] : '', 25), ENT_QUOTES, 'UTF-8') ?></span><span class="content-card__action" aria-hidden="true">مطالعه مقاله ←</span></div>
        </a>
      </article><?php endforeach; ?>
    </div><?php else: ?><div class="empty-state"><div class="empty-state__icon">⌕</div><h2>هنوز مقاله‌ای منتشر نشده است</h2><p>به‌زودی مطالب جدید در این بخش نمایش داده می‌شود.</p></div><?php endif; ?>
  </div></section>

  <?php
  $features = [
    ['img' => $dataHeader['img_one'] ?? '', 'title' => 'جست‌وجوی خدمات بر اساس شهر', 'desc' => 'شهر موردنظرتان را انتخاب کنید و مطالب مرتبط با تعمیرات و ارائه‌دهندگان خدمات همان منطقه را بررسی کنید.'],
    ['img' => $dataHeader['img_two'] ?? '', 'title' => 'راهنمای عیب‌یابی لوازم خانگی', 'desc' => 'پیش از انتخاب تعمیرکار، با نشانه‌های خرابی یخچال، لباسشویی، کولر گازی و دیگر دستگاه‌ها آشنا شوید.'],
    ['img' => $dataHeader['img_tree'] ?? '', 'title' => 'مقایسه اطلاعات برندها و خدمات', 'desc' => 'مقالات برندها، راهنمای نگهداری و شیوه‌های ارتباط با منتشرکنندگان خدمات را در یک مجموعه پیدا کنید.']
  ];  ?>
  <section class="site-section site-section--soft"><div class="site-container">
    <div class="section-head"><div><span class="site-eyebrow">چرا ما</span><h2 class="site-title">خدماتی برای یک انتخاب مطمئن‌تر</h2></div></div>
    <div class="feature-grid"><?php foreach ($features as $feature): ?><?php $featureImage=(string)$feature['img']; $featureUrl=$featureImage!==''&&preg_match('#^https?://#i',$featureImage)?$featureImage:assets($featureImage); ?>
      <article class="feature-card"<?= $featureImage !== '' ? ' style="background-image:url(\'' . htmlspecialchars($featureUrl, ENT_QUOTES, 'UTF-8') . '\')"' : '' ?>><div class="feature-card__content"><h3><?= htmlspecialchars(strip_tags((string)$feature['title']), ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars(strip_tags((string)$feature['desc']), ENT_QUOTES, 'UTF-8') ?></p></div></article>
    <?php endforeach; ?></div>
  </div></section>

  <section class="site-section site-section--dark"><div class="site-container">
    <div class="section-head"><div><span class="site-eyebrow">برندها و نمایندگی‌ها</span><h2 class="site-title">تازه‌ترین مطالب برندها</h2><p class="site-subtitle">محتوای مرتبط با برندها، خدمات و راهنمای انتخاب مرکز مناسب.</p></div></div>
    <?php if (!empty($brands) && is_array($brands)): ?><div class="content-grid"><?php foreach ($brands as $item): ?><?php $url=assets((isset($item['slug'])?$item['slug']:'').'/'.(int)$item['id']); ?>
      <article class="content-card" data-reveal><a class="content-card__link" href="<?= $url ?>"><div class="content-card__media"><img src="<?= assets($item['img']) ?>" alt="" width="640" height="400" loading="lazy" decoding="async"></div><div class="content-card__body"><span class="content-card__meta">مطلب برند</span><h3><?= htmlspecialchars(clean_display_text(isset($item['title'])?$item['title']:''),ENT_QUOTES,'UTF-8') ?></h3><span class="content-card__summary"><?= htmlspecialchars(excerpt_text(isset($item['content'])?$item['content']:'',25),ENT_QUOTES,'UTF-8') ?></span><span class="content-card__action" aria-hidden="true">مشاهده مطلب ←</span></div></a></article>
    <?php endforeach; ?></div><?php endif; ?>
  </div></section>

  <section class="site-section"><div class="site-container">
    <div class="section-head"><div><span class="site-eyebrow">برندهای تحت پوشش</span><h2 class="site-title">دسترسی سریع به برندها</h2></div></div>
    <?php if (!empty($brand) && is_array($brand)): ?><div class="brand-grid"><?php foreach ($brand as $item): ?><a class="brand-card" href="<?= assets('brands/categories/'.(int)$item['id'].'/1') ?>"><img src="<?= assets($item['img']) ?>" alt="<?= htmlspecialchars(clean_display_text(isset($item['name'])?$item['name']:''),ENT_QUOTES,'UTF-8') ?>" width="320" height="240" loading="lazy" decoding="async"><strong><?= htmlspecialchars(clean_display_text(isset($item['name'])?$item['name']:''),ENT_QUOTES,'UTF-8') ?></strong><span><?= htmlspecialchars(excerpt_text(isset($item['des'])?$item['des']:'',10),ENT_QUOTES,'UTF-8') ?></span></a><?php endforeach; ?></div><?php endif; ?>
  </div></section>

  <section class="site-section--compact"><div class="site-container"><div class="cta-panel"><div><h2>خدمات شهر خودتان را پیدا کنید</h2><p>با انتخاب شهر، مستقیماً به خدمات و مسیرهای مرتبط دسترسی پیدا کنید.</p></div><div class="cta-panel__actions"><a class="btn-site btn-site--accent" href="<?= assets('cities') ?>">مشاهده شهرها</a><?php if($phone!==''): ?><a class="btn-site btn-site--light" href="tel:<?= htmlspecialchars($phoneHref,ENT_QUOTES,'UTF-8') ?>">تماس: <?= htmlspecialchars($phone,ENT_QUOTES,'UTF-8') ?></a><?php endif; ?></div></div></div></section>
</main>
<?php require BASE_PATH . '/them/app/layout/footer.php'; ?>
<?php require BASE_PATH . '/them/app/layout/js.php'; ?>
</body>
</html>