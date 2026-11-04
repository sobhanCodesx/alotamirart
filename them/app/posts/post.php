<?php
$found = isset($err) && $err === true && !empty($post);
$title = $found && isset($post['title']) ? clean_display_text($post['title']) : 'مقاله یافت نشد';
$description = $found && isset($post['description']) ? clean_display_text($post['description']) : '';
$authorName = !empty($user['name']) ? clean_display_text($user['name']) : 'تحریریه';
require_once BASE_PATH . '/them/app/posts/article-contact-helper.php';
$selectedContact = alo_article_contact($found ? $post : [], is_array($user ?? null) ? $user : []);
$contactPhone = $selectedContact['number'] !== '' ? clean_display_text($selectedContact['number']) : '';
$contactPhoneHref = $selectedContact['tel'];
$contactIsCustom = $selectedContact['custom'];
$authorAvatar = !empty($user['img']) ? $user['img'] : 'them/admin/dist/img/avatar.png';
$authorProfile = !empty($user['id']) && !empty($user['user_name'])
    ? assets('profile/'.$user['user_name'].'/'.(int)$user['id'])
    : '';
$authorRole = !empty($user['title']) ? clean_display_text((string)$user['title']) : '';
$authorBio = !empty($user['bio']) ? clean_display_text((string)$user['bio']) : '';
if ($authorBio !== '' && mb_strlen($authorBio, 'UTF-8') > 160) {
    $authorBio = mb_substr($authorBio, 0, 157, 'UTF-8') . '…';
}
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
<?php if ($found): ?><link rel="canonical" href="https://alotamiratchi.ir/post/<?= (int)$post['id'] ?>"><?php endif; ?>
<?php if($found): ?>
<meta property="og:type" content="article">
<meta property="og:title" content="<?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>">
<?php if(!empty($post['img'])): ?><meta property="og:image" content="<?= htmlspecialchars(assets($post['img']),ENT_QUOTES,'UTF-8') ?>"><?php endif; ?>
<?php endif; ?>
<?php if ($found):
$articleSchema = [
  '@context' => 'https://schema.org',
  '@type' => 'BlogPosting',
  'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => 'https://alotamiratchi.ir/post/'.(int)$post['id']],
  'headline' => $title,
  'author' => ['@type' => 'Person', 'name' => $authorName],
  'publisher' => ['@type' => 'Organization', 'name' => 'الو تعمیراتچی', 'url' => 'https://alotamiratchi.ir/'],
];
if ($description !== '') $articleSchema['description'] = $description;
if (!empty($post['img'])) $articleSchema['image'] = [assets($post['img'])];
if (!empty($post['created_at']) && strtotime((string)$post['created_at']) !== false) {
  $articleSchema['datePublished'] = date(DATE_ATOM, strtotime((string)$post['created_at']));
}
?>
<script type="application/ld+json"><?= json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?></script>
<?php endif; ?>
<?php require BASE_PATH.'/them/app/layout/heading.php'; ?>
<link rel="stylesheet" href="<?= assets('assets/css/lalezar-articles.css') ?>?v=1">
<link rel="stylesheet" href="<?= assets('assets/css/article-heading-colors.css') ?>?v=1">
<?php $aloAuthorCssVersion = @filemtime(BASE_PATH . '/public/src/css/alo-article-author.css') ?: '1'; ?>
<link rel="stylesheet" href="<?= assets('public/src/css/alo-article-author.css') ?>?v=<?= urlencode((string)$aloAuthorCssVersion) ?>">
</head>
<body class="detail-page detail-page--article<?= $contactPhone!==''?' detail-page--has-contact':'' ?>">
<?php require BASE_PATH.'/them/app/layout/header.php'; ?>

<main id="main-content" class="detail-main">
<div class="site-container">
<?php if($found): ?>
  <div class="detail-layout">
    <article class="detail-card">
      <?php if(!empty($post['img'])): ?>
        <div class="detail-cover-wrap">
          <img class="detail-cover" src="<?= assets($post['img']) ?>" alt="<?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>" width="1200" height="638" fetchpriority="high" decoding="async">
        </div>
      <?php endif; ?>

      <div class="detail-body">
        <div class="breadcrumbs detail-breadcrumbs"><a href="<?= assets('/') ?>">خانه</a> / مقاله</div>
        <?php if(isset($_SESSION['role'])&&(int)$_SESSION['role']===1): ?><a class="a        <section class="alo-author-showcase" aria-labelledby="alo-author-heading">
          <div class="alo-author-showcase__accent" aria-hidden="true"></div>
          <div class="alo-author-showcase__intro">
            <?php if ($authorProfile !== ''): ?>
              <a class="alo-author-showcase__avatar" href="<?= htmlspecialchars($authorProfile, ENT_QUOTES, 'UTF-8') ?>"
                 aria-label="<?= htmlspecialchars('مشاهده رزومه ' . $authorName, ENT_QUOTES, 'UTF-8') ?>">
                <img src="<?= assets($authorAvatar) ?>" alt="<?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?>"
                     width="96" height="96" loading="lazy" decoding="async">
              </a>
            <?php else: ?>
              <span class="alo-author-showcase__avatar">
                <img src="<?= assets($authorAvatar) ?>" alt="<?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?>"
                     width="96" height="96" loading="lazy" decoding="async">
              </span>
            <?php endif; ?>
            <div class="alo-author-showcase__identity">
              <span class="alo-author-showcase__eyebrow"><span aria-hidden="true">✦</span> معرفی نویسنده این مقاله</span>
              <h2 id="alo-author-heading"><?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?></h2>
              <p><?= htmlspecialchars($authorRole !== '' ? $authorRole : 'نویسنده و منتشرکننده محتوا در الو تعمیراتچی', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          </div>
          <?php if ($authorBio !== ''): ?>
            <p class="alo-author-showcase__bio"><?= htmlspecialchars($authorBio, ENT_QUOTES, 'UTF-8') ?></p>
          <?php endif; ?>
          <div class="alo-author-showcase__footer">
            <div class="alo-author-showcase__stats" aria-label="اطلاعات مقاله">
              <span><span aria-hidden="true">◉</span> <?= (int)$view ?> بازدید</span>
              <?php if (!empty($post['created_at'])): ?>
                <span><span aria-hidden="true">▦</span> <time><?= htmlspecialchars((string)$post['created_at'], ENT_QUOTES, 'UTF-8') ?></time></span>
              <?php endif; ?>
            </div>
            <div class="alo-author-showcase__actions">
              <?php if ($authorProfile !== ''): ?>
                <a class="alo-author-showcase__resume" href="<?= htmlspecialchars($authorProfile, ENT_QUOTES, 'UTF-8') ?>">
                  <span aria-hidden="true">▤</span> مشاهده رزومه و مقالات <span aria-hidden="true">←</span>
                </a>
              <?php endif; ?>
              <?php if ($contactPhone !== ''): ?>
                <a class="alo-author-showcase__contact" href="#alo-article-contact">راه‌های تماس <span aria-hidden="true">↓</span></a>
              <?php endif; ?>
            </div>
          </div>
        </section>
if; ?>
          </div>
          <div class="detail-meta-chips">
            <span><?= (int)$view ?> بازدید</span>
            <?php if(!empty($post['created_at'])): ?><span><?= htmlspecialchars((string)$post['created_at'],ENT_QUOTES,'UTF-8') ?></span><?php endif; ?>
          </div>
        </div>

        <?php if($contactPhone!==''): ?>
          <div id="alo-article-contact" class="detail-contact-inline alo-article-contact">
            <div class="detail-contact-inline__identity">
              <span class="detail-contact-inline__icon">☎</span>
              <div><strong><?php if($contactIsCustom): ?>تماس مستقیم این مقاله<?php else: ?>ارتباط مستقیم با <?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?><?php endif; ?></strong><small>برای تماس، شماره زیر را لمس کنید</small></div>
            </div>
            <a href="tel:<?= htmlspecialchars($contactPhoneHref,ENT_QUOTES,'UTF-8') ?>"><?= htmlspecialchars($contactPhone,ENT_QUOTES,'UTF-8') ?></a>
          </div>
        <?php endif; ?>

        <div class="article-content detail-content"><?= isset($post['content']) ? $post['content'] : '' ?></div>
      </div>
    </article>

    <aside class="detail-aside">
      <?php if($contactPhone!==''): ?>
      <div class="detail-contact-card">
        <div class="detail-contact-card__owner">
          <img src="<?= assets($authorAvatar) ?>" alt="" width="54" height="54">
          <div><span>تماس مستقیم</span><strong><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></strong></div>
        </div>
        <p><?= $contactIsCustom ? "برای ارتباط با شماره تماس اختصاصی این مقاله، از دکمه زیر استفاده کنید." : "برای ارتباط سریع با صاحب این مطلب، مستقیماً تماس بگیرید." ?></p>
        <a class="detail-call-button" href="tel:<?= htmlspecialchars($contactPhoneHref,ENT_QUOTES,'UTF-8') ?>"><span>☎</span><strong><?= htmlspecialchars($contactPhone,ENT_QUOTES,'UTF-8') ?></strong></a>
        <?php if($authorProfile!==''): ?><a class="detail-profile-link" href="<?= htmlspecialchars($authorProfile, ENT_QUOTES, 'UTF-8') ?>">مشاهده رزومه و مقالات ←</a><?php endif; ?>
      </div>
      <?php endif; ?>
      <?php require BASE_PATH.'/them/app/layout/sidbar.php'; ?>
    </aside>
  </div>
<?php else: ?>
  <section class="site-section"><div class="empty-state"><div class="empty-state__icon">404</div><h2>مقاله مورد نظر پیدا نشد</h2><p>ممکن است آدرس تغییر کرده باشد یا مقاله دیگر در دسترس نباشد.</p><div style="margin-top:18px"><a class="btn-site btn-site--primary" href="<?= assets('/') ?>">بازگشت به صفحه اصلی</a></div></div></section>
<?php endif; ?>
</div>
</main>

<?php if($found && $contactPhone!==''): ?>
<div class="mobile-contact-bar" role="region" aria-label="تماس سریع">
  <div class="mobile-contact-bar__owner">
    <img src="<?= assets($authorAvatar) ?>" alt="" width="42" height="42">
    <div><span><?= $contactIsCustom ? "تماس مقاله" : "تماس با" ?></span><strong><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></strong></div>
  </div>
  <?php if ($authorProfile !== ''): ?>
    <a class="alo-mobile-resume" href="<?= htmlspecialchars($authorProfile, ENT_QUOTES, 'UTF-8') ?>">رزومه <span aria-hidden="true">↗</span></a>
  <?php endif; ?>
  <a class="alo-mobile-call" href="tel:<?= htmlspecialchars($contactPhoneHref,ENT_QUOTES,'UTF-8') ?>"><span aria-hidden="true">☎</span> تماس</a>
</div>
<?php endif; ?>

<?php require BASE_PATH.'/them/app/layout/footer.php'; ?>
<?php require BASE_PATH.'/them/app/layout/js.php'; ?>
</body>
</html>