<?php
$found = isset($err) && $err === true && !empty($post);
$title = $found && isset($post['title']) ? clean_display_text($post['title']) : 'مطلب یافت نشد';
$description = $found ? clean_display_text(isset($post['des'])?$post['des']:(isset($post['description'])?$post['description']:'')) : '';
$brandName = $found && isset($post['namebrand']) ? clean_display_text($post['namebrand']) : 'برند';
$authorName = !empty($user['name']) ? clean_display_text($user['name']) : 'تحریریه';
$authorAvatar = !empty($user['img']) ? $user['img'] : 'them/admin/dist/img/avatar.png';
$authorProfile = !empty($user['id']) && !empty($user['user_name'])
    ? assets('profile/'.$user['user_name'].'/'.(int)$user['id'])
    : '';
$authorRole = !empty($user['title']) ? clean_display_text((string)$user['title']) : '';

$contact = '';
$contactLabel = '';
if($found && !empty($post['contact_number'])) {
    $contact = clean_display_text($post['contact_number']);
    $contactLabel = 'تماس مستقیم این مطلب';
} elseif(!empty($user['phon'])) {
    $contact = clean_display_text($user['phon']);
    $contactLabel = 'تماس با منتشرکننده';
} elseif(!empty($dataFooter['phon'])) {
    $contact = clean_display_text($dataFooter['phon']);
    $contactLabel = 'تماس با پشتیبانی';
}
require_once BASE_PATH . '/them/app/posts/article-contact-helper.php';
if ($found && alo_chalus_nowshahr_heat_contact((string)($post['title'] ?? ''))) {
    $contact = '09901068051';
    $contactLabel = 'تماس مستقیم این مطلب';
}
if ($found && aloEightCityMatch((string)($post['title'] ?? ''))) {
    $contact = '09108731535';
    $contactLabel = 'تماس مستقیم این مطلب';
}
$contactHref = $contact !== '' ? preg_replace('/[^\d+]/','',$contact) : '';
$canonicalBrandSlug = $found ? trim((string)($post['slug'] ?? ''), '/') : '';
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?> | <?= htmlspecialchars(isset($dataSeo['title'])?clean_display_text($dataSeo['title']):'الو تعمیراتچی',ENT_QUOTES,'UTF-8') ?></title>
<?php if ($found && $description === ''): ?>
<?php
$plainContent = clean_display_text($post['content'] ?? '');
$description = mb_substr($plainContent !== '' ? $plainContent : $title . ' | اطلاعات و جزئیات این مطلب در الو تعمیراتچی', 0, 155, 'UTF-8');
?>
<?php endif; ?>
<?php if($description!==''): ?><meta name="description" content="<?= htmlspecialchars($description,ENT_QUOTES,'UTF-8') ?>"><?php endif; ?>
<meta name="robots" content="<?= $found?'index,follow':'noindex,follow' ?>">
<?php if ($found && $canonicalBrandSlug !== ''): ?><link rel="canonical" href="https://alotamiratchi.ir/<?= htmlspecialchars(rawurlencode($canonicalBrandSlug), ENT_QUOTES, 'UTF-8') ?>/<?= (int)$post['id'] ?>"><?php endif; ?>
<?php if($found): ?>
<meta property="og:type" content="article">
<meta property="og:title" content="<?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>">
<?php if(!empty($post['img'])): ?><meta property="og:image" content="<?= htmlspecialchars(assets($post['img']),ENT_QUOTES,'UTF-8') ?>"><?php endif; ?>
<?php endif; ?>
<?php require BASE_PATH.'/them/app/layout/heading.php'; ?>
<?php $aloAuthorCssVersion = @filemtime(BASE_PATH . '/public/src/css/alo-article-author.css') ?: '1'; ?>
<link rel="stylesheet" href="<?= assets('public/src/css/alo-article-author.css') ?>?v=<?= urlencode((string)$aloAuthorCssVersion) ?>">
<?php $aloV3CssVersion = @filemtime(BASE_PATH . '/public/src/css/alo-detail-v3.css') ?: '1'; ?>
<link rel="stylesheet" href="<?= assets('public/src/css/alo-detail-v3.css') ?>?v=<?= urlencode((string)$aloV3CssVersion) ?>">

</head>
<body class="detail-page detail-page--brand<?= $contact!==''?' detail-page--has-contact':'' ?>">
<?php require BASE_PATH.'/them/app/layout/header.php'; ?>

<main id="main-content" class="detail-main">
<div class="site-container">
<?php if($found): ?>
  <div class="detail-layout">
    <article class="detail-card">
      <?php if(!empty($post['img'])): ?>
        <div class="detail-cover-wrap detail-cover-wrap--brand">
          <img class="detail-cover" src="<?= assets($post['img']) ?>" alt="<?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>" width="1200" height="638" fetchpriority="high" decoding="async">
          <span class="detail-brand-badge"><?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></span>
        </div>
      <?php endif; ?>

      <div class="detail-body">
        <div class="breadcrumbs detail-breadcrumbs"><a href="<?= assets('/') ?>">خانه</a> / <?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></div>
        <?php if(isset($_SESSION['role'])&&(int)$_SESSION['role']===1): ?><a class="admin-edit" target="_blank" rel="noopener" href="<?= assets('admin/brands/post/update/'.(int)$post['id']) ?>">ویرایش این مطلب در پنل ادمین ↗</a><?php endif; ?>

        <span class="detail-kicker detail-kicker--brand">مطلب برند · <?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></span>
        <h1 class="detail-title"><?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?></h1>

        <section class="alo-author-showcase alo-detail-v3-author" aria-labelledby="alo-brand-author-heading">
          <div class="alo-author-showcase__accent" aria-hidden="true"></div>
          <div class="alo-author-showcase__intro">
            <?php if($authorProfile!==''): ?><a class="alo-author-showcase__avatar" href="<?= htmlspecialchars($authorProfile,ENT_QUOTES,'UTF-8') ?>" aria-label="مشاهده رزومه منتشرکننده">
            <?php else: ?><span class="alo-author-showcase__avatar"><?php endif; ?>
              <img src="<?= assets($authorAvatar) ?>" alt="<?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?>" width="96" height="96" loading="lazy" decoding="async">
            <?php if($authorProfile!==''): ?></a><?php else: ?></span><?php endif; ?>
            <div class="alo-author-showcase__identity">
              <span class="alo-author-showcase__eyebrow">✦ آشنایی با منتشرکننده</span>
              <h2 id="alo-brand-author-heading"><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></h2>
              <p><?= htmlspecialchars($authorRole!==''?$authorRole:'انتشار مطالب تخصصی در الو تعمیراتچی',ENT_QUOTES,'UTF-8') ?></p>
            </div>
          </div>
          <div class="alo-author-showcase__footer">
            <div class="alo-author-showcase__stats">
              <span>◉ <?= (int)$view ?> بازدید</span>
              <span>✦ <?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></span>
            </div>
            <div class="alo-author-showcase__actions">
              <?php if($authorProfile!==''): ?>
                <a class="alo-author-showcase__resume" href="<?= htmlspecialchars($authorProfile,ENT_QUOTES,'UTF-8') ?>">مشاهده رزومه و مقالات <span aria-hidden="true">←</span></a>
              <?php endif; ?>
              <?php if($contact!==''): ?><a class="alo-author-showcase__contact" href="#alo-brand-contact">راه‌های تماس <span aria-hidden="true">↓</span></a><?php endif; ?>
            </div>
          </div>
        </section>

        <?php if($contact!==''): ?>
          <div id="alo-brand-contact" class="detail-contact-inline detail-contact-inline--brand">
            <div class="detail-contact-inline__identity">
              <span class="detail-contact-inline__icon">☎</span>
              <div><strong><?= htmlspecialchars($contactLabel,ENT_QUOTES,'UTF-8') ?></strong><small>برای ارتباط سریع، شماره را لمس کنید</small></div>
            </div>
            <a href="tel:<?= htmlspecialchars($contactHref,ENT_QUOTES,'UTF-8') ?>"><?= htmlspecialchars($contact,ENT_QUOTES,'UTF-8') ?></a>
          </div>
        <?php endif; ?>

        <div class="article-content detail-content"><?= isset($post['content']) ? $post['content'] : '' ?></div>
      </div>
    </article>

    <aside class="detail-aside">
      <?php if($contact!==''): ?>
      <div class="detail-contact-card detail-contact-card--brand">
        <div class="detail-contact-card__owner">
          <img src="<?= assets($authorAvatar) ?>" alt="" width="54" height="54">
          <div><span><?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></span><strong><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></strong></div>
        </div>
        <p><?= htmlspecialchars($contactLabel,ENT_QUOTES,'UTF-8') ?>؛ برای ارتباط سریع همین حالا تماس بگیرید.</p>
        <a class="detail-call-button" href="tel:<?= htmlspecialchars($contactHref,ENT_QUOTES,'UTF-8') ?>"><span>☎</span><strong><?= htmlspecialchars($contact,ENT_QUOTES,'UTF-8') ?></strong></a>
        <?php if($authorProfile!==''): ?><a class="detail-profile-link" href="<?= $authorProfile ?>">مشاهده رزومه و مقالات ←</a><?php endif; ?>
      </div>
      <?php endif; ?>
      <?php require BASE_PATH.'/them/app/layout/sidbar.php'; ?>
    </aside>
  </div>
<?php else: ?>
  <section class="site-section"><div class="empty-state"><div class="empty-state__icon">404</div><h2>مطلب مورد نظر پیدا نشد</h2><p>ممکن است آدرس تغییر کرده باشد یا این محتوا دیگر در دسترس نباشد.</p><div style="margin-top:18px"><a class="btn-site btn-site--primary" href="<?= assets('/') ?>">بازگشت به صفحه اصلی</a></div></div></section>
<?php endif; ?>
</div>
</main>

<?php if($found && $contact!==''): ?>
<div class="mobile-contact-bar mobile-contact-bar--brand" role="region" aria-label="تماس سریع">
  <div class="mobile-contact-bar__owner">
    <img src="<?= assets($authorAvatar) ?>" alt="" width="42" height="42">
    <div><span><?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></span><strong><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></strong></div>
  </div>
  <?php if ($authorProfile !== ''): ?>
    <a class="alo-mobile-resume" href="<?= htmlspecialchars($authorProfile,ENT_QUOTES,'UTF-8') ?>">رزومه ↗</a>
  <?php endif; ?>
  <a class="alo-mobile-call" href="tel:<?= htmlspecialchars($contactHref,ENT_QUOTES,'UTF-8') ?>"><span aria-hidden="true">☎</span> تماس</a>
</div>
<?php endif; ?>

<?php require BASE_PATH.'/them/app/layout/footer.php'; ?>
<?php require BASE_PATH.'/them/app/layout/js.php'; ?>
<script src="<?= assets('public/src/js/alo-detail-v3.js') ?>" defer></script>
</body>
</html>