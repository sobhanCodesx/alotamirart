<?php
$categoryTitle = isset($item['title']) ? strip_tags((string)$item['title']) : 'مقالات';
$currentPage = isset($page) ? max(1,(int)$page) : 1;
$totalPages = isset($pages) ? max(1,(int)$pages) : 1;
?>
<!doctype html><html lang="fa" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= htmlspecialchars($categoryTitle,ENT_QUOTES,'UTF-8') ?> | <?= htmlspecialchars(isset($dataSeo['title'])?strip_tags($dataSeo['title']):'الو تعمیراتچی',ENT_QUOTES,'UTF-8') ?></title>
<meta name="description" content="<?= htmlspecialchars('مقالات دسته‌بندی '.$categoryTitle,ENT_QUOTES,'UTF-8') ?>">
<meta name="robots" content="index,follow">
<?php require BASE_PATH.'/them/app/layout/heading.php'; ?>
</head><body>
<?php require BASE_PATH.'/them/app/layout/header.php'; ?>
<main id="main-content">
<section class="page-hero"><div class="site-container"><div class="breadcrumbs"><a href="<?= assets('/') ?>">خانه</a> / مقالات</div><h1><?= htmlspecialchars($categoryTitle,ENT_QUOTES,'UTF-8') ?></h1><p>آخرین مطالب این دسته‌بندی، صفحه <?= $currentPage ?> از <?= $totalPages ?>.</p></div></section>
<section class="site-section"><div class="site-container">
<?php if(!empty($post)&&is_array($post)): ?><div class="content-grid">
<?php foreach($post as $b): ?><article class="content-card">
<a class="content-card__media" href="<?= assets('post/'.(int)$b['id']) ?>"><img src="<?= assets($b['img']) ?>" alt="<?= htmlspecialchars(clean_display_text(isset($b['title'])?$b['title']:''),ENT_QUOTES,'UTF-8') ?>" width="640" height="400" loading="lazy" decoding="async"></a>
<div class="content-card__body"><div class="content-card__meta"><?= htmlspecialchars($categoryTitle,ENT_QUOTES,'UTF-8') ?></div><h3><a href="<?= assets('post/'.(int)$b['id']) ?>"><?= htmlspecialchars(clean_display_text(isset($b['title'])?$b['title']:''),ENT_QUOTES,'UTF-8') ?></a></h3><p><?= htmlspecialchars(excerpt_text(isset($b['content'])?$b['content']:'',25),ENT_QUOTES,'UTF-8') ?></p><a class="content-card__action" href="<?= assets('post/'.(int)$b['id']) ?>">ادامه مطلب ←</a></div>
</article><?php endforeach; ?></div>
<?php else: ?><div class="empty-state"><div class="empty-state__icon">⌕</div><h2>مقاله‌ای یافت نشد</h2><p>در حال حاضر محتوایی در این دسته‌بندی منتشر نشده است.</p></div><?php endif; ?>
<?php if($totalPages>1): ?><nav class="pagination-site" aria-label="صفحه‌بندی">
<?php if($currentPage>1): ?><a href="<?= assets('posts/categories/'.(int)$item['id'].'/'.($currentPage-1)) ?>">قبلی</a><?php else: ?><span class="is-disabled">قبلی</span><?php endif; ?>
<?php for($i=max(1,$currentPage-2);$i<=min($totalPages,$currentPage+2);$i++): ?><a class="<?= $i===$currentPage?'is-active':'' ?>" href="<?= assets('posts/categories/'.(int)$item['id'].'/'.$i) ?>"><?= $i ?></a><?php endfor; ?>
<?php if($currentPage<$totalPages): ?><a href="<?= assets('posts/categories/'.(int)$item['id'].'/'.($currentPage+1)) ?>">بعدی</a><?php else: ?><span class="is-disabled">بعدی</span><?php endif; ?>
</nav><?php endif; ?>
</div></section>
</main>
<?php require BASE_PATH.'/them/app/layout/footer.php'; ?><?php require BASE_PATH.'/them/app/layout/js.php'; ?>
</body></html>