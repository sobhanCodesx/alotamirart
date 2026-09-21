<?php
$found = isset($err) && $err === true && !empty($post);
$title = $found && isset($post['title']) ? strip_tags((string)$post['title']) : 'مقاله یافت نشد';
$description = $found && isset($post['description']) ? strip_tags((string)$post['description']) : '';
$authorName = !empty($user['name']) ? strip_tags((string)$user['name']) : 'تحریریه';
?>
<!doctype html><html lang="fa" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?> | <?= htmlspecialchars(isset($dataSeo['title'])?strip_tags($dataSeo['title']):'الو تعمیراتچی',ENT_QUOTES,'UTF-8') ?></title>
<?php if($description!==''): ?><meta name="description" content="<?= htmlspecialchars($description,ENT_QUOTES,'UTF-8') ?>"><?php endif; ?>
<meta name="robots" content="<?= $found?'index,follow':'noindex,follow' ?>">
<?php if($found): ?><meta property="og:type" content="article"><meta property="og:title" content="<?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>"><?php if(!empty($post['img'])): ?><meta property="og:image" content="<?= htmlspecialchars(assets($post['img']),ENT_QUOTES,'UTF-8') ?>"><?php endif; ?><?php endif; ?>
<?php require BASE_PATH.'/them/app/layout/heading.php'; ?>
</head><body>
<?php require BASE_PATH.'/them/app/layout/header.php'; ?>
<main id="main-content"><div class="site-container">
<?php if($found): ?>
<div class="article-layout">
<article class="article-card">
<?php if(!empty($post['img'])): ?><img class="article-cover" src="<?= assets($post['img']) ?>" alt="<?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?>" width="1200" height="638" fetchpriority="high" decoding="async"><?php endif; ?>
<div class="article-body">
<div class="breadcrumbs"><a href="<?= assets('/') ?>">خانه</a> / مقاله</div>
<?php if(isset($_SESSION['role'])&&(int)$_SESSION['role']===1): ?><a class="admin-edit" target="_blank" rel="noopener" href="<?= assets('admin/posts/update/'.(int)$post['id']) ?>">ویرایش این مقاله در پنل ادمین ↗</a><?php endif; ?>
<h1><?= htmlspecialchars($title,ENT_QUOTES,'UTF-8') ?></h1>
<div class="article-meta">
<span>نویسنده: <?php if(!empty($user['id'])&&!empty($user['user_name'])): ?><a href="<?= assets('profile/'.$user['user_name'].'/'.(int)$user['id']) ?>"><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?></a><?php else: ?><?= htmlspecialchars($authorName,ENT_QUOTES,'UTF-8') ?><?php endif; ?></span>
<span><?= (int)$view ?> بازدید</span>
<?php if(!empty($post['created_at'])): ?><span><?= htmlspecialchars((string)$post['created_at'],ENT_QUOTES,'UTF-8') ?></span><?php endif; ?>
</div>
<div class="article-content"><?= isset($post['content']) ? $post['content'] : '' ?></div>
</div>
</article>
<aside><?php require BASE_PATH.'/them/app/layout/sidbar.php'; ?></aside>
</div>
<?php else: ?>
<section class="site-section"><div class="empty-state"><div class="empty-state__icon">404</div><h2>مقاله مورد نظر پیدا نشد</h2><p>ممکن است آدرس تغییر کرده باشد یا مقاله دیگر در دسترس نباشد.</p><div style="margin-top:18px"><a class="btn-site btn-site--primary" href="<?= assets('/') ?>">بازگشت به صفحه اصلی</a></div></div></section>
<?php endif; ?>
</div></main>
<?php require BASE_PATH.'/them/app/layout/footer.php'; ?><?php require BASE_PATH.'/them/app/layout/js.php'; ?>
</body></html>