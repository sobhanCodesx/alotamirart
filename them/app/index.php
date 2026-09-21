<?php
$siteTitle = isset($dataSeo['title']) ? strip_tags((string)$dataSeo['title']) : 'الو تعمیراتچی';
$siteDescription = isset($dataSeo['description']) ? strip_tags((string)$dataSeo['description']) : '';
$heroRaw = isset($dataSeo['header']) ? (string)$dataSeo['header'] : '';
$heroUrl = $heroRaw !== '' && preg_match('#^https?://#i', $heroRaw) ? $heroRaw : assets($heroRaw);
$phone = isset($dataFooter['phon']) ? strip_tags((string)$dataFooter['phon']) : '';
$phoneHref = preg_replace('/[^\d+]/', '', $phone);
$loginMessage = flash('login');
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <title><?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <?php if ($siteDescription !== ''): ?><meta name="description" content="<?= htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
  <link rel="canonical" href="<?= htmlspecialchars(assets('/'), ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?>">
  <?php if ($siteDescription !== ''): ?><meta property="og:description" content="<?= htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
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
        <h1><?= htmlspecialchars(isset($dataSeo['title_h1']) ? strip_tags((string)$dataSeo['title_h1']) : $siteTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="hero__lead"><?= htmlspecialchars(isset($dataSeo['title_h2']) ? strip_tags((string)$dataSeo['title_h2']) : $siteDescription, ENT_QUOTES, 'UTF-8') ?></p>
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
        <a class="content-card__media" href="<?= assets('post/' . (int)$item['id']) ?>"><img src="<?= assets($item['img']) ?>" alt="<?= htmlspecialchars(isset($item['title']) ? strip_tags($item['title']) : '', ENT_QUOTES, 'UTF-8') ?>" width="640" height="400" loading="lazy" decoding="async"></a>
        <div class="content-card__body"><div class="content-card__meta">مقاله آموزشی</div><h3><a href="<?= assets('post/' . (int)$item['id']) ?>"><?= htmlspecialchars(isset($item['title']) ? strip_tags($item['title']) : '', ENT_QUOTES, 'UTF-8') ?></a></h3><p><?= htmlspecialchars(limit_words(strip_tags(isset($item['content']) ? $item['content'] : ''), 25), ENT_QUOTES, 'UTF-8') ?></p><a class="content-card__action" href="<?= assets('post/' . (int)$item['id']) ?>">ادامه مطلب ←</a></div>
      </article><?php endforeach; ?>
    </div><?php else: ?><div class="empty-state"><div class="empty-state__icon">⌕</div><h2>هنوز مقاله‌ای منتشر نشده است</h2><p>به‌زودی مطالب جدید در این بخش نمایش داده می‌شود.</p></div><?php endif; ?>
  </div></section>

  <?php
  $features = [
    ['img' => isset($dataHeader['img_one']) ? $dataHeader['img_one'] : '', 'title' => isset($dataHeader['title_one']) ? $dataHeader['title_one'] : '', 'desc' => isset($dataHeader['description_one']) ? $dataHeader['description_one'] : ''],
    ['img' => isset($dataHeader['img_two']) ? $dataHeader['img_two'] : '', 'title' => isset($dataHeader['title_two']) ? $dataHeader['title_two'] : '', 'desc' => isset($dataHeader['description_two']) ? $dataHeader['description_two'] : ''],
    ['img' => isset($dataHeader['img_tree']) ? $dataHeader['img_tree'] : '', 'title' => isset($dataHeader['title_tree']) ? $dataHeader['title_tree'] : '', 'desc' => isset($dataHeader['description_tree']) ? $dataHeader['description_tree'] : '']
  ];
  ?>
  <section class="site-section site-section--soft"><div class="site-container">
    <div class="section-head"><div><span class="site-eyebrow">چرا ما</span><h2 class="site-title">خدماتی برای یک انتخاب مطمئن‌تر</h2></div></div>
    <div class="feature-grid"><?php foreach ($features as $feature): ?><?php $featureImage=(string)$feature['img']; $featureUrl=$featureImage!==''&&preg_match('#^https?://#i',$featureImage)?$featureImage:assets($featureImage); ?>
      <article class="feature-card"<?= $featureImage !== '' ? ' style="background-image:url(\'' . htmlspecialchars($featureUrl, ENT_QUOTES, 'UTF-8') . '\')"' : '' ?>><div class="feature-card__content"><h3><?= htmlspecialchars(strip_tags((string)$feature['title']), ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars(strip_tags((string)$feature['desc']), ENT_QUOTES, 'UTF-8') ?></p></div></article>
    <?php endforeach; ?></div>
  </div></section>

  <section class="site-section site-section--dark"><div class="site-container">
    <div class="section-head"><div><span class="site-eyebrow">برندها و نمایندگی‌ها</span><h2 class="site-title">تازه‌ترین مطالب برندها</h2><p class="site-subtitle">محتوای مرتبط با برندها، خدمات و راهنمای انتخاب مرکز مناسب.</p></div></div>
    <?php if (!empty($brands) && is_array($brands)): ?><div class="content-grid"><?php foreach ($brands as $item): ?><?php $url=assets((isset($item['slug'])?$item['slug']:'').'/'.(int)$item['id']); ?>
      <article class="content-card" data-reveal><a class="content-card__media" href="<?= $url ?>"><img src="<?= assets($item['img']) ?>" alt="<?= htmlspecialchars(isset($item['title'])?strip_tags($item['title']):'',ENT_QUOTES,'UTF-8') ?>" width="640" height="400" loading="lazy" decoding="async"></a><div class="content-card__body"><div class="content-card__meta">مطلب برند</div><h3><a href="<?= $url ?>"><?= htmlspecialchars(isset($item['title'])?strip_tags($item['title']):'',ENT_QUOTES,'UTF-8') ?></a></h3><p><?= htmlspecialchars(limit_words(strip_tags(isset($item['content'])?$item['content']:''),25),ENT_QUOTES,'UTF-8') ?></p><a class="content-card__action" href="<?= $url ?>">مشاهده مطلب ←</a></div></article>
    <?php endforeach; ?></div><?php endif; ?>
  </div></section>

  <section class="site-section"><div class="site-container">
    <div class="section-head"><div><span class="site-eyebrow">برندهای تحت پوشش</span><h2 class="site-title">دسترسی سریع به برندها</h2></div></div>
    <?php if (!empty($brand) && is_array($brand)): ?><div class="brand-grid"><?php foreach ($brand as $item): ?><a class="brand-card" href="<?= assets('brands/categories/'.(int)$item['id'].'/1') ?>"><img src="<?= assets($item['img']) ?>" alt="<?= htmlspecialchars(isset($item['name'])?strip_tags($item['name']):'',ENT_QUOTES,'UTF-8') ?>" width="320" height="240" loading="lazy" decoding="async"><strong><?= htmlspecialchars(isset($item['name'])?strip_tags($item['name']):'',ENT_QUOTES,'UTF-8') ?></strong><span><?= htmlspecialchars(limit_words(strip_tags(isset($item['des'])?$item['des']:''),10),ENT_QUOTES,'UTF-8') ?></span></a><?php endforeach; ?></div><?php endif; ?>
  </div></section>

  <section class="site-section--compact"><div class="site-container"><div class="cta-panel"><div><h2>خدمات شهر خودتان را پیدا کنید</h2><p>با انتخاب شهر، مستقیماً به خدمات و مسیرهای مرتبط دسترسی پیدا کنید.</p></div><div class="cta-panel__actions"><a class="btn-site btn-site--accent" href="<?= assets('cities') ?>">مشاهده شهرها</a><?php if($phone!==''): ?><a class="btn-site btn-site--light" href="tel:<?= htmlspecialchars($phoneHref,ENT_QUOTES,'UTF-8') ?>">تماس: <?= htmlspecialchars($phone,ENT_QUOTES,'UTF-8') ?></a><?php endif; ?></div></div></div></section>
</main>
<?php require BASE_PATH . '/them/app/layout/footer.php'; ?>
<?php require BASE_PATH . '/them/app/layout/js.php'; ?>
</body>
</html>