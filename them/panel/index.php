<?php $msg=flash('msg');$saved=flash('saveuser');$avatar=!empty($aUser['img'])?$aUser['img']:'them/admin/dist/img/avatar.png'; ?>
<!doctype html><html lang="fa" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="robots" content="noindex,nofollow">
<title>پنل کاربری | <?= htmlspecialchars((string)getByUser('name'),ENT_QUOTES,'UTF-8') ?></title>
<?php require BASE_PATH.'/them/app/layout/heading.php'; ?><link rel="stylesheet" href="<?= assets('public/src/css/user-panel.css') ?>">
</head><body class="user-panel-page">
<?php require BASE_PATH.'/them/app/layout/header.php'; ?>
<main id="main-content" class="user-panel-shell"><div class="site-container">
<div class="user-panel-head"><div><h1>پنل کاربری</h1><p>مدیریت اطلاعات حساب و محتوای شما</p></div><div class="user-panel-actions"><a class="btn-site btn-site--light" href="<?= assets('profile/'.getByUser('user_name').'/'.getByUser('id')) ?>">مشاهده پروفایل</a></div></div>
<div class="user-panel-grid">
<nav class="user-panel-nav" aria-label="منوی پنل"><div class="user-panel-nav__user"><strong><?= htmlspecialchars((string)getByUser('name'),ENT_QUOTES,'UTF-8') ?></strong><span><?= htmlspecialchars((string)getByUser('user_name'),ENT_QUOTES,'UTF-8') ?></span></div>
<a class="is-active" href="<?= assets('panelcp') ?>">اطلاعات حساب <span>←</span></a><a href="<?= assets('user/post/1') ?>">مقالات من <span>←</span></a><a href="<?= assets('user/posts/create') ?>">مقاله جدید <span>+</span></a><a href="<?= assets('user/brand/1') ?>">محتوای برند <span>←</span></a><a href="<?= assets('userbrand/create') ?>">محتوای برند جدید <span>+</span></a></nav>
<section class="user-panel-card"><h2>اطلاعات حساب</h2><p class="user-panel-card__lead">اطلاعات اصلی حساب را ویرایش کنید. برای تغییر رمز، فقط رمز جدید را وارد کنید.</p>
<?php if($msg!==''): ?><div class="user-alert user-alert--danger"><?= htmlspecialchars($msg,ENT_QUOTES,'UTF-8') ?></div><?php endif; ?><?php if($saved!==''): ?><div class="user-alert user-alert--success"><?= htmlspecialchars($saved,ENT_QUOTES,'UTF-8') ?></div><?php endif; ?>
<form action="<?= assets('update/profile/'.(int)$aUser['id']) ?>" method="post" enctype="multipart/form-data">
<div class="user-avatar-edit"><img src="<?= assets($avatar) ?>" alt="" width="90" height="90"><div class="user-form-group" style="flex:1"><label class="user-form-label" for="profile-img">تصویر پروفایل</label><input id="profile-img" class="user-input" style="padding-top:10px" type="file" name="img" accept="image/*"><span class="user-help">در صورت نیاز به تغییر تصویر، فایل جدید انتخاب کنید.</span></div></div>
<div class="user-form-grid">
<div class="user-form-group"><label class="user-form-label" for="p-name">نام و نام خانوادگی</label><input class="user-input" id="p-name" required name="name" value="<?= htmlspecialchars(checkValue($aUser,'name'),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="user-form-group"><label class="user-form-label" for="p-user">نام کاربری</label><input class="user-input" id="p-user" required name="user_name" value="<?= htmlspecialchars(checkValue($aUser,'user_name'),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="user-form-group"><label class="user-form-label" for="p-email">ایمیل</label><input class="user-input" id="p-email" type="email" required name="email" value="<?= htmlspecialchars(checkValue($aUser,'email'),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="user-form-group"><label class="user-form-label" for="p-phone">شماره تماس</label><input class="user-input" id="p-phone" required name="phon" value="<?= htmlspecialchars(checkValue($aUser,'phon'),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="user-form-group user-form-group--full"><label class="user-form-label" for="p-password">رمز عبور جدید</label><input class="user-input" id="p-password" type="password" name="password" autocomplete="new-password" placeholder="اگر نمی‌خواهید تغییر کند، خالی بگذارید"><span class="user-help">رمز فعلی هرگز در فرم نمایش داده نمی‌شود.</span></div>
</div><div style="margin-top:22px"><button class="btn-site btn-site--primary" type="submit">ذخیره تغییرات</button></div>
</form></section>
</div></div></main>
<?php require BASE_PATH.'/them/app/layout/footer.php'; ?><?php require BASE_PATH.'/them/app/layout/js.php'; ?>
</body></html>