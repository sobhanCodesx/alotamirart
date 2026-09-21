<?php
$found = isset($err) && $err === true && !empty($post);
$title = $found && isset($post['title']) ? clean_display_text($post['title']) : 'مقاله یافت نشد';
$description = $found && isset($post['description']) ? clean_display_text($post['description']) : '';
$authorName = !empty($user['name']) ? clean_display_text($user['name']) : 'تحریریه';
$authorPhone = !empty($user['phon']) ? clean_display_text($user['phon']) : '';
$authorPhoneHref = $authorPhone !== '' ? preg_replace('/[^\d+]/', '', $authorPhone) : '';
$authorAvatar = !empty($user['img']) ? $user['img'] : 'them/admin/dist/img/avatar.png';
$authorProfile = !empty($user['id']) && !empty($user['user_name'])
    ? assets('profile/'.$user['user_name'].'/'.(int)$user['id'])
    : '';
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?> | <?= htmlspecialchars(isset($dataSeo['title'])?clean_display_text($dataSeo['title']):'الو تعمیراتچی',ENT_QUOTES,'UTF-8') ?></title>
<?php if($description!==''): ?><meta name="description" content="<?= htmlspecialchars($description,ENT_QUOTES,'UTF-8') ?>"><?php endif; ?>
<meta name="robots" content="<?= $found?'index,follow':'noindex,follow' ?>">
<?php if($found): ?>
<meta property="og:type" content="article">
<meta property="og:title" content="<?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>">
<?php if(!empty($post['img'])): ?><meta property="og:image" content="<?= htmlspecialchars(assets($post['img']),ENT_QUOTES,'UTF-8') ?>"><?php endif; ?>
<?php endif; ?>
<?php require BASE_PATH.'/them/app/layout/heading.php'; ?>
</head>
<body class="detail-page detail-page--article<?= $authorPhone!==''?' detail-page--has-contact':'' ?>">
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
        <?php if(isset($_SESSION['role'])&&(int)$_SESSION['role']===1): ?><a class="admin-edit" target="_blank" rel="noopener" href="<?= assets('admin/posts/update/'.(int)$post['id']) ?>">ویرایش این مقاله در پنل ادمین ↗</a><?php endif; ?>

        <span class="detail-kicker">مقاله آموزشی</span>
        <h1 class="detail-title"><?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?></h1>

        <div class="detail-owner">
          <?php if($authorProfile!==''): ?><a class="detail-owner__avatar" href="<?= $authorProfile ?>"><?php else: ?><span class="detail-owner__avatar"><?php endif; ?>
            <img src="<?= assets($authorAvatar) ?>" alt="<?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?>" width="52" height="52">
          <?php if($authorProfile!==''): ?></a><?php else: ?></span><?php endif; ?>
          <div class="detail-owner__info">
            <span>نویسنده</span>
            <?php if($authorProfile!==''): ?><a href="<?= $authorProfile ?>"><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></a><?php else: ?><strong><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></strong><?php endif; ?>
          </div>
          <div class="detail-meta-chips">
            <span><?= (int)$view ?> بازدید</span>
            <?php if(!empty($post['created_at'])): ?><span><?= htmlspecialchars((string)$post['created_at'],ENT_QUOTES,'UTF-8') ?></span><?php endif; ?>
          </div>
        </div>

        <?php if($authorPhone!==''): ?>
          <div class="detail-contact-inline">
            <div class="detail-contact-inline__identity">
              <span class="detail-contact-inline__icon">☎</span>
              <div><strong>ارتباط مستقیم با <?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></strong><small>برای تماس، شماره زیر را لمس کنید</small></div>
            </div>
            <a href="tel:<?= htmlspecialchars($authorPhoneHref,ENT_QUOTES,'UTF-8') ?>"><?= htmlspecialchars($authorPhone,ENT_QUOTES,'UTF-8') ?></a>
          </div>
        <?php endif; ?>

        <div class="article-content detail-content"><?= isset($post['content']) ? $post['content'] : '' ?></div>
      </div>
    </article>

    <aside class="detail-aside">
      <?php if($authorPhone!==''): ?>
      <div class="detail-contact-card">
        <div class="detail-contact-card__owner">
          <img src="<?= assets($authorAvatar) ?>" alt="" width="54" height="54">
          <div><span>تماس مستقیم</span><strong><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></strong></div>
        </div>
        <p>برای ارتباط سریع با صاحب این مطلب، مستقیماً تماس بگیرید.</p>
        <a class="detail-call-button" href="tel:<?= htmlspecialchars($authorPhoneHref,ENT_QUOTES,'UTF-8') ?>"><span>☎</span><strong><?= htmlspecialchars($authorPhone,ENT_QUOTES,'UTF-8') ?></strong></a>
        <?php if($authorProfile!==''): ?><a class="detail-profile-link" href="<?= $authorProfile ?>">مشاهده پروفایل ←</a><?php endif; ?>
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

<?php if($found && $authorPhone!==''): ?>
<div class="mobile-contact-bar" role="region" aria-label="تماس سریع">
  <div class="mobile-contact-bar__owner">
    <img src="<?= assets($authorAvatar) ?>" alt="" width="42" height="42">
    <div><span>تماس با</span><strong><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></strong></div>
  </div>
  <a href="tel:<?= htmlspecialchars($authorPhoneHref,ENT_QUOTES,'UTF-8') ?>"><span aria-hidden="true">☎</span> تماس</a>
</div>
<?php endif; ?>

<?php require BASE_PATH.'/them/app/layout/footer.php'; ?>
<?php require BASE_PATH.'/them/app/layout/js.php'; ?>
</body>
</html>