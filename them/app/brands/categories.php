<?php
$brandName = isset($item['name']) ? strip_tags((string)$item['name']) : 'برند';
$brandDescription = isset($item['des']) ? strip_tags((string)$item['des']) : '';
$currentPage = isset($page) ? max(1,(int)$page) : 1;
$totalPages = isset($pages) ? max(1,(int)$pages) : 1;
?>
<!doctype html><html lang="fa" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?> | <?= htmlspecialchars(isset($dataSeo['title'])?strip_tags($dataSeo['title']):'الو تعمیراتچی',ENT_QUOTES,'UTF-8') ?></title>
<?php if($brandDescription!==''): ?><meta name="description" content="<?= htmlspecialchars(limit_words($brandDescription,28),ENT_QUOTES,'UTF-8') ?>"><?php endif; ?>
<meta name="robots" content="index,follow">
<?php require BASE_PATH.'/them/app/layout/heading.php'; ?>
</head><body>
<?php require BASE_PATH.'/them/app/layout/header.php'; ?>
<main id="main-content">
<section class="page-hero"><div class="site-container">
<div class="breadcrumbs"><a href="<?= assets('/') ?>">خانه</a> / برندها</div>
<?php if(!empty($item['img'])): ?><img src="<?= assets($item['img']) ?>" alt="<?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?>" width="120" height="90" style="width:120px;height:90px;object-fit:cover;border-radius:18px;margin-bottom:18px" decoding="async"><?php endif; ?>
<h1><?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></h1>
<?php if($brandDescription!==''): ?><p><?= htmlspecialchars($brandDescription,ENT_QUOTES,'UTF-8') ?></p><?php endif; ?>
</div></section>
<section class="site-section"><div class="site-container">
<div class="section-head"><div><span class="site-eyebrow">مطالب برند</span><h2 class="site-title">آخرین مطالب <?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></h2></div></div>
<?php if(!empty($brands)&&is_array($brands)): ?><div class="content-grid">
<?php foreach($brands as $b): ?><?php $url=assets((isset($b['slug'])?$b['slug']:'').'/'.(int)$b['id']); ?><article class="content-card">
<a class="content-card__media" href="<?= $url ?>"><img src="<?= assets($b['img']) ?>" alt="<?= htmlspecialchars(isset($b['title'])?strip_tags($b['title']):'',ENT_QUOTES,'UTF-8') ?>" width="640" height="400" loading="lazy" decoding="async"></a>
<div class="content-card__body"><div class="content-card__meta"><?= htmlspecialchars($brandName,ENT_QUOTES,'UTF-8') ?></div><h3><a href="<?= $url ?>"><?= htmlspecialchars(isset($b['title'])?strip_tags($b['title']):'',ENT_QUOTES,'UTF-8') ?></a></h3><p><?= htmlspecialchars(limit_words(strip_tags(isset($b['content'])?$b['content']:''),25),ENT_QUOTES,'UTF-8') ?></p><a class="content-card__action" href="<?= $url ?>">مشاهده مطلب ←</a></div>
</article><?php endforeach; ?></div>
<?php else: ?><div class="empty-state"><div class="empty-state__icon">⌕</div><h2>مطلبی یافت نشد</h2><p>در حال حاضر محتوایی برای این برند منتشر نشده است.</p></div><?php endif; ?>
<?php if($totalPages>1): ?><nav class="pagination-site" aria-label="صفحه‌بندی">
<?php if($currentPage>1): ?><a href="<?= assets('brands/categories/'.(int)$item['id'].'/'.($currentPage-1)) ?>">قبلی</a><?php else: ?><span class="is-disabled">قبلی</span><?php endif; ?>
<?php for($i=max(1,$currentPage-2);$i<=min($totalPages,$currentPage+2);$i++): ?><a class="<?= $i===$currentPage?'is-active':'' ?>" href="<?= assets('brands/categories/'.(int)$item['id'].'/'.$i) ?>"><?= $i ?></a><?php endfor; ?>
<?php if($currentPage<$totalPages): ?><a href="<?= assets('brands/categories/'.(int)$item['id'].'/'.($currentPage+1)) ?>">بعدی</a><?php else: ?><span class="is-disabled">بعدی</span><?php endif; ?>
</nav><?php endif; ?>
</div></section>
</main>
<?php require BASE_PATH.'/them/app/layout/footer.php'; ?><?php require BASE_PATH.'/them/app/layout/js.php'; ?>
</body></html>