<?php
$error = flash('msg');
$name = isset($_SESSION['name_temp']) ? (string)$_SESSION['name_temp'] : '';
$username = isset($_SESSION['user_name_temp']) ? (string)$_SESSION['user_name_temp'] : '';
$email = isset($_SESSION['email_temp']) ? (string)$_SESSION['email_temp'] : '';
$phone = isset($_SESSION['phon_temp']) ? (string)$_SESSION['phon_temp'] : '';
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>ثبت‌نام | <?= htmlspecialchars(isset($dataSeo['title']) ? strip_tags($dataSeo['title']) : 'الو تعمیراتچی', ENT_QUOTES, 'UTF-8') ?></title>
<meta name="robots" content="noindex,follow">
<?php require BASE_PATH . '/them/app/layout/heading.php'; ?>
</head>
<body>
<?php require BASE_PATH . '/them/app/layout/header.php'; ?>
<main id="main-content" class="auth-page">
<div class="auth-shell">
<aside class="auth-aside">
<div><span class="hero__badge">ایجاد حساب</span><h1>به جمع کاربران بپیوندید</h1><p>حساب خود را بسازید تا پروفایل و امکانات محتوایی در دسترس شما باشد.</p></div>
<ul><li>✓ ثبت‌نام سریع و ساده</li><li>✓ طراحی بهینه برای موبایل</li><li>✓ دسترسی یکپارچه به حساب</li></ul>
</aside>
<section class="auth-card">
<h2>ثبت‌نام</h2>
<p class="auth-card__lead">اطلاعات زیر را تکمیل کنید. فیلدهای اصلی الزامی هستند.</p>
<?php if ($error !== ''): ?><div class="alert-site alert-site--danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form action="<?= assets('registered') ?>" method="post" class="form-grid">
<div class="form-group"><label class="form-label" for="reg-name">نام و نام خانوادگی</label><input class="form-control-site" type="text" name="name" id="reg-name" value="<?= htmlspecialchars($name,ENT_QUOTES,'UTF-8') ?>" autocomplete="name" required></div>
<div class="form-group"><label class="form-label" for="reg-user">نام کاربری</label><input class="form-control-site" type="text" name="user_name" id="reg-user" value="<?= htmlspecialchars($username,ENT_QUOTES,'UTF-8') ?>" autocomplete="username" required></div>
<div class="form-group"><label class="form-label" for="reg-email">ایمیل</label><input class="form-control-site" type="email" name="email" id="reg-email" value="<?= htmlspecialchars($email,ENT_QUOTES,'UTF-8') ?>" autocomplete="email" required></div>
<div class="form-group"><label class="form-label" for="reg-phone">شماره تماس</label><input class="form-control-site" type="tel" name="phon" id="reg-phone" value="<?= htmlspecialchars($phone,ENT_QUOTES,'UTF-8') ?>" autocomplete="tel"></div>
<div class="form-group"><label class="form-label" for="reg-pass">رمز عبور</label><input class="form-control-site" type="password" name="password" id="reg-pass" autocomplete="new-password" required></div>
<div class="form-group"><label class="form-label" for="reg-pass2">تکرار رمز عبور</label><input class="form-control-site" type="password" name="password_two" id="reg-pass2" autocomplete="new-password" required></div>
<div class="form-group form-group--full"><button class="btn-site btn-site--primary btn-site--block" type="submit">ساخت حساب</button></div>
</form>
<p class="auth-footer-note">قبلاً ثبت‌نام کرده‌اید؟ <a href="<?= assets('login') ?>">وارد شوید</a></p>
</section>
</div>
</main>
<?php require BASE_PATH . '/them/app/layout/footer.php'; ?>
<?php require BASE_PATH . '/them/app/layout/js.php'; ?>
</body>
</html>