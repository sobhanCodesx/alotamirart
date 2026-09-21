<?php
$error = flash('login_error');
$success = flash('saveuser');
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>ورود | <?= htmlspecialchars(isset($dataSeo['title']) ? strip_tags($dataSeo['title']) : 'الو تعمیراتچی', ENT_QUOTES, 'UTF-8') ?></title>
<meta name="robots" content="noindex,follow">
<?php require BASE_PATH . '/them/app/layout/heading.php'; ?>
</head>
<body>
<?php require BASE_PATH . '/them/app/layout/header.php'; ?>
<main id="main-content" class="auth-page">
<div class="auth-shell">
<aside class="auth-aside">
<div><span class="hero__badge">حساب کاربری</span><h1>خوش برگشتید</h1><p>برای مدیریت محتوای خود و دسترسی به امکانات حساب وارد شوید.</p></div>
<ul><li>✓ دسترسی سریع به پروفایل</li><li>✓ مدیریت محتوای کاربری</li><li>✓ تجربه یکپارچه در موبایل و دسکتاپ</li></ul>
</aside>
<section class="auth-card">
<h2>ورود به حساب</h2>
<p class="auth-card__lead">نام کاربری و رمز عبور خود را وارد کنید.</p>
<?php if ($error !== ''): ?><div class="alert-site alert-site--danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php if ($success !== ''): ?><div class="alert-site alert-site--success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form action="<?= assets('logined') ?>" method="post" class="form-grid">
<div class="form-group form-group--full"><label class="form-label" for="login-user">نام کاربری</label><input class="form-control-site" type="text" name="user_name" id="login-user" autocomplete="username" required></div>
<div class="form-group form-group--full"><label class="form-label" for="login-password">رمز عبور</label><input class="form-control-site" type="password" name="password" id="login-password" autocomplete="current-password" required></div>
<div class="form-group form-group--full"><button class="btn-site btn-site--primary btn-site--block" type="submit">ورود</button></div>
</form>
<p class="auth-footer-note">حساب ندارید؟ <a href="<?= assets('register') ?>">ثبت‌نام کنید</a></p>
</section>
</div>
</main>
<?php require BASE_PATH . '/them/app/layout/footer.php'; ?>
<?php require BASE_PATH . '/them/app/layout/js.php'; ?>
</body>
</html>