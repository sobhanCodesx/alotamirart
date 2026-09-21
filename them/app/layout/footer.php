<?php
$footerAbout = isset($dataFooter['about_description']) ? trim(strip_tags((string)$dataFooter['about_description'])) : '';
$footerPhone = isset($dataFooter['phon']) ? trim(strip_tags((string)$dataFooter['phon'])) : '';
$footerEmail = isset($dataFooter['email']) ? trim(strip_tags((string)$dataFooter['email'])) : '';
$footerInstagram = isset($dataFooter['instagram']) ? trim(strip_tags((string)$dataFooter['instagram'])) : '';
$footerPhoneHref = preg_replace('/[^\d+]/', '', $footerPhone);
?>
<footer class="site-footer">
  <div class="site-container site-footer__main">
    <div>
      <h2><?= htmlspecialchars(isset($dataSeo['title']) ? $dataSeo['title'] : 'الو تعمیراتچی', ENT_QUOTES, 'UTF-8') ?></h2>
      <p><?= htmlspecialchars($footerAbout !== '' ? $footerAbout : 'راهنمای انتخاب خدمات و دسترسی سریع به محتوای تخصصی تعمیرات لوازم خانگی.', ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div>
      <h2>دسترسی سریع</h2>
      <ul class="footer-links">
        <li><a href="<?= assets('/') ?>">صفحه اصلی</a></li>
        <li><a href="<?= assets('cities') ?>">شهرهای تحت پوشش</a></li>
        <li><a href="<?= assets('login') ?>">ورود کاربران</a></li>
        <li><a href="<?= assets('register') ?>">ایجاد حساب</a></li>
      </ul>
    </div>

    <div>
      <h2>ارتباط با ما</h2>
      <div class="footer-contact">
        <?php if ($footerPhone !== ''): ?><a href="tel:<?= htmlspecialchars($footerPhoneHref, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($footerPhone, ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
        <?php if ($footerEmail !== ''): ?><a href="mailto:<?= htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
        <?php if ($footerInstagram !== ''): ?><span>اینستاگرام: @<?= htmlspecialchars(ltrim($footerInstagram, '@'), ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
      </div>
    </div>
  </div>

  <div class="site-footer__bottom">
    <div class="site-container site-footer__bottom-inner">
      <span>© <?= date('Y') ?> تمامی حقوق محفوظ است.</span>
      <span>طراحی سبک، سریع و سازگار با موبایل و دسکتاپ</span>
    </div>
  </div>
</footer>
<button type="button" class="back-to-top" data-back-top aria-label="بازگشت به بالا">↑</button>