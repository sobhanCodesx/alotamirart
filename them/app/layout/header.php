<?php
$siteTitle = isset($dataSeo['title']) ? clean_display_text($dataSeo['title']) : 'الو تعمیراتچی';
$logo = isset($dataSeo['logo']) ? $dataSeo['logo'] : 'public/src/img/logo.png';
$phone = isset($dataFooter['phon']) ? clean_display_text($dataFooter['phon']) : '';
$email = isset($dataFooter['email']) ? clean_display_text($dataFooter['email']) : '';
$phoneHref = preg_replace('/[^\d+]/', '', $phone);
?>
<a class="skip-link" href="#main-content">رفتن به محتوای اصلی</a>
<div class="site-topbar">
  <div class="site-container site-topbar__inner">
    <div class="site-topbar__group">
      <?php if ($phone !== ''): ?><a href="tel:<?= htmlspecialchars($phoneHref, ENT_QUOTES, 'UTF-8') ?>">مشاوره و پشتیبانی: <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
      <?php if ($email !== ''): ?><a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
    </div>
    <div class="site-topbar__group"><span>خدمات تخصصی تعمیر لوازم خانگی</span></div>
  </div>
</div>

<header class="site-header">
  <div class="site-container site-header__inner">
    <a class="site-logo" href="<?= assets('/') ?>" aria-label="<?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?>">
      <img src="<?= assets($logo) ?>" alt="<?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?>" width="150" height="50">
    </a>

    <nav class="site-nav" id="mobile-site-nav" data-site-nav aria-label="منوی اصلی">
      <div class="site-nav__mobile-head">
        <strong>منوی سایت</strong>
        <button class="mobile-nav-close" type="button" data-menu-close aria-label="بستن منو"><span aria-hidden="true">×</span></button>
      </div>
      <ul class="site-nav__list">
        <li><a href="<?= assets('/') ?>">خانه</a></li>
        <li><a href="<?= assets('cities') ?>">شهرهای تحت پوشش</a></li>
        <?php if (!empty($menu) && is_array($menu)): ?>
          <?php foreach ($menu as $m): ?>
            <li><a href="<?= assets('posts/categories/' . (int)$m['id'] . '/1') ?>"><?= htmlspecialchars(clean_display_text(isset($m['title']) ? $m['title'] : ''), ENT_QUOTES, 'UTF-8') ?></a></li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>

      <div class="site-nav__mobile-tools">
        <form method="post" action="<?= assets('search/1') ?>" class="mobile-nav-search" role="search">
          <input name="search" type="search" placeholder="جستجو در سایت" aria-label="جستجو در سایت">
          <button type="submit">جستجو</button>
        </form>

        <?php if (isset($_SESSION['name'])): ?>
          <div class="mobile-account-card">
            <div class="mobile-account-card__name"><?= htmlspecialchars((string)$_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="mobile-account-links">
              <?php if (isset($_SESSION['role']) && (int)$_SESSION['role'] === 1): ?><a href="<?= assets('admin/dashboard') ?>">پنل ادمین</a><?php endif; ?>
              <?php if (isset($_SESSION['w']) && (int)$_SESSION['w'] === 1): ?>
                <a href="<?= assets('panelcp/') ?>">پنل کاربری</a>
                <a href="<?= assets('profile/'.getByUser('user_name').'/'.getByUser('id')) ?>">پروفایل</a>
                <a href="<?= assets('user/posts/create') ?>">مقاله جدید</a>
                <a href="<?= assets('user/post/1') ?>">مقالات من</a>
                <a href="<?= assets('userbrand/create') ?>">محتوای برند جدید</a>
                <a href="<?= assets('user/brand/1') ?>">محتوای برند من</a>
              <?php endif; ?>
              <a href="<?= assets('logout') ?>">خروج</a>
            </div>
          </div>
        <?php else: ?>
          <div class="mobile-auth-actions">
            <a class="btn-site btn-site--primary" href="<?= assets('login') ?>">ورود</a>
            <a class="btn-site btn-site--light" href="<?= assets('register') ?>">ثبت‌نام</a>
          </div>
        <?php endif; ?>
      </div>
    </nav>
    <button class="site-nav-backdrop" type="button" data-nav-backdrop aria-label="بستن منو" tabindex="-1"></button>

    <div class="site-header__actions">
      <button class="icon-btn search-trigger" type="button" data-search-toggle aria-expanded="false" aria-label="باز کردن جستجو">⌕</button>

      <?php if (isset($_SESSION['name'])): ?>
        <details class="account-menu">
          <summary><?= htmlspecialchars((string)$_SESSION['name'], ENT_QUOTES, 'UTF-8') ?> ▾</summary>
          <div class="account-menu__dropdown">
            <?php if (isset($_SESSION['role']) && (int)$_SESSION['role'] === 1): ?><a href="<?= assets('admin/dashboard') ?>">پنل ادمین</a><?php endif; ?>
            <?php if (isset($_SESSION['w']) && (int)$_SESSION['w'] === 1): ?>
              <a href="<?= assets('panelcp/') ?>">پنل کاربری</a>
              <a href="<?= assets('profile/'.getByUser('user_name').'/'.getByUser('id')) ?>">پروفایل</a>
              <a href="<?= assets('user/posts/create') ?>">نوشتن مقاله</a>
              <a href="<?= assets('user/post/1') ?>">مقالات من</a>
              <a href="<?= assets('userbrand/create') ?>">ثبت محتوای برند</a>
              <a href="<?= assets('user/brand/1') ?>">محتوای برند من</a>
            <?php endif; ?>
            <a href="<?= assets('logout') ?>">خروج</a>
          </div>
        </details>
      <?php else: ?>
        <div class="auth-actions">
          <a href="<?= assets('login') ?>">ورود</a>
          <a href="<?= assets('register') ?>">ثبت‌نام</a>
        </div>
      <?php endif; ?>

      <button class="icon-btn menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-controls="mobile-site-nav" aria-label="باز کردن منو">
        <span class="hamburger-icon" aria-hidden="true"><span></span></span>
      </button>
    </div>
  </div>

  <div class="search-panel" data-search-panel>
    <div class="site-container search-panel__inner">
      <form method="post" action="<?= assets('search/1') ?>" class="search-form" role="search">
        <input name="search" type="search" placeholder="نام شهر، برند یا موضوع را جستجو کنید" aria-label="جستجو">
        <button class="btn-site btn-site--primary" type="submit">جستجو</button>
      </form>
    </div>
  </div>
</header>