<?php
$name=isset($user['name'])?strip_tags((string)$user['name']):'کاربر';
$bio=isset($user['bio'])?trim(strip_tags((string)$user['bio'])):'';
$title=isset($user['title'])?trim(strip_tags((string)$user['title'])):'';
$avatar=!empty($user['img'])?$user['img']:'them/admin/dist/img/avatar.png';
?>
<!doctype html><html lang="fa" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= htmlspecialchars($name,ENT_QUOTES,'UTF-8') ?> | پروفایل</title>
<?php if($bio!==''): ?><meta name="description" content="<?= htmlspecialchars($bio,ENT_QUOTES,'UTF-8') ?>"><?php endif; ?>
<?php require BASE_PATH.'/them/app/layout/heading.php'; ?>
</head><body>
<?php require BASE_PATH.'/them/app/layout/header.php'; ?>
<main id="main-content">
<section class="profile-hero"><div class="site-container profile-intro">
<img class="profile-avatar" src="<?= assets($avatar) ?>" alt="<?= htmlspecialchars($name,ENT_QUOTES,'UTF-8') ?>" width="140" height="140">
<div><h1><?= htmlspecialchars($name,ENT_QUOTES,'UTF-8') ?></h1>
<?php if($title!==''): ?><p style="font-weight:800;color:#fde68a;margin-bottom:6px"><?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?></p><?php endif; ?>
<p><?= htmlspecialchars($bio!==''?$bio:'اطلاعات معرفی این کاربر هنوز تکمیل نشده است.',ENT_QUOTES,'UTF-8') ?></p>
<div class="profile-meta">
<?php if(!empty($user['city'])): ?><span>شهر: <?= htmlspecialchars($user['city'],ENT_QUOTES,'UTF-8') ?></span><?php endif; ?>
<?php if(!empty($user['web'])): ?><a href="<?= htmlspecialchars($user['web'],ENT_QUOTES,'UTF-8') ?>" rel="nofollow noopener" target="_blank">وب‌سایت ↗</a><?php endif; ?>
<?php if(!empty($user['instagram'])): ?><span>اینستاگرام: @<?= htmlspecialchars(ltrim($user['instagram'],'@'),ENT_QUOTES,'UTF-8') ?></span><?php endif; ?>
</div></div>
</div></section>
<section class="site-section"><div class="site-container profile-columns">
<div><div class="section-head"><div><span class="site-eyebrow">نوشته‌ها</span><h2 class="site-title">آخرین مقالات</h2></div></div>
<?php if(!empty($posts)): ?><div style="display:grid;gap:14px"><?php foreach($posts as $item): ?><article class="content-card" style="display:grid;grid-template-columns:130px minmax(0,1fr)">
<a class="content-card__media" style="aspect-ratio:auto;height:100%" href="<?= assets('post/'.(int)$item['id']) ?>"><img src="<?= assets($item['img']) ?>" alt="<?= htmlspecialchars(strip_tags($item['title']),ENT_QUOTES,'UTF-8') ?>" width="260" height="180" loading="lazy"></a>
<div class="content-card__body"><h3><a href="<?= assets('post/'.(int)$item['id']) ?>"><?= htmlspecialchars(strip_tags($item['title']),ENT_QUOTES,'UTF-8') ?></a></h3><p><?= htmlspecialchars(limit_words(strip_tags(isset($item['content'])?$item['content']:''),18),ENT_QUOTES,'UTF-8') ?></p><span class="content-card__meta"><?= (int)(isset($item['view'])?$item['view']:0) ?> بازدید</span></div></article><?php endforeach; ?></div>
<?php else: ?><div class="empty-state"><p>هنوز مقاله‌ای منتشر نشده است.</p></div><?php endif; ?></div>
<div><div class="section-head"><div><span class="site-eyebrow">برندها</span><h2 class="site-title">محتوای برند</h2></div></div>
<?php if(!empty($brand)): ?><div style="display:grid;gap:14px"><?php foreach($brand as $item): ?><?php $url=assets((isset($item['slug'])?$item['slug']:'').'/'.(int)$item['id']); ?><article class="content-card" style="display:grid;grid-template-columns:130px minmax(0,1fr)">
<a class="content-card__media" style="aspect-ratio:auto;height:100%" href="<?= $url ?>"><img src="<?= assets($item['img']) ?>" alt="<?= htmlspecialchars(strip_tags($item['title']),ENT_QUOTES,'UTF-8') ?>" width="260" height="180" loading="lazy"></a>
<div class="content-card__body"><h3><a href="<?= $url ?>"><?= htmlspecialchars(strip_tags($item['title']),ENT_QUOTES,'UTF-8') ?></a></h3><p><?= htmlspecialchars(limit_words(strip_tags(isset($item['content'])?$item['content']:''),18),ENT_QUOTES,'UTF-8') ?></p><span class="content-card__meta"><?= (int)(isset($item['view'])?$item['view']:0) ?> بازدید</span></div></article><?php endforeach; ?></div>
<?php else: ?><div class="empty-state"><p>هنوز محتوای برندی منتشر نشده است.</p></div><?php endif; ?></div>
</div></section>
</main>
<?php require BASE_PATH.'/them/app/layout/footer.php'; ?><?php require BASE_PATH.'/them/app/layout/js.php'; ?>
</body></html>