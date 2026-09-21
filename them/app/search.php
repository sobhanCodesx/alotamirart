<?php
$query = isset($result) ? trim(strip_tags((string)$result)) : '';
$title = $query !== '' ? 'نتایج جستجو برای «' . $query . '»' : 'نتایج جستجو';
$currentPage = isset($page) ? max(1, (int)$page) : 1;
$totalPages = isset($pages) ? max(1, (int)$pages) : 1;
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars(isset($dataSeo['title']) ? strip_tags($dataSeo['title']) : 'الو تعمیراتچی', ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="robots" content="noindex,follow">
  <?php require BASE_PATH . '/them/app/layout/heading.php'; ?>
</head>
<body>
<?php require BASE_PATH . '/them/app/layout/header.php'; ?>
<main id="main-content">
  <section class="page-hero"><div class="site-container">
    <div class="breadcrumbs"><a href="<?= assets('/') ?>">خانه</a> / جستجو</div>
    <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>نتایج مرتبط با عبارت جستجو شده در محتوای برندها نمایش داده می‌شود.</p>
  </div></section>

  <section class="site-section"><div class="site-container">
    <?php if (!empty($post) && is_array($post)): ?>
      <div class="content-grid">
        <?php foreach ($post as $item): ?><?php $url=assets((isset($item['slug'])?$item['slug']:'').'/'.(int)$item['id']); ?>
          <article class="content-card">
            <a class="content-card__media" href="<?= $url ?>"><img src="<?= assets($item['img']) ?>" alt="<?= htmlspecialchars(isset($item['title'])?strip_tags($item['title']):'',ENT_QUOTES,'UTF-8') ?>" width="640" height="400" loading="lazy" decoding="async"></a>
            <div class="content-card__body"><div class="content-card__meta">نتیجه جستجو</div><h3><a href="<?= $url ?>"><?= htmlspecialchars(isset($item['title'])?strip_tags($item['title']):'',ENT_QUOTES,'UTF-8') ?></a></h3><p><?= htmlspecialchars(limit_words(strip_tags(isset($item['content'])?$item['content']:''),25),ENT_QUOTES,'UTF-8') ?></p><a class="content-card__action" href="<?= $url ?>">مشاهده نتیجه ←</a></div>
          </article>
        <?php endforeach; ?>
      </div>
      <?php if ($totalPages > 1): ?><nav class="pagination-site" aria-label="صفحه‌بندی نتایج">
        <?php if ($currentPage > 1): ?><form class="pagination-form" method="post" action="<?= assets('search/'.($currentPage-1)) ?>"><input type="hidden" name="search" value="<?= htmlspecialchars($query,ENT_QUOTES,'UTF-8') ?>"><button type="submit">قبلی</button></form><?php else: ?><span class="is-disabled">قبلی</span><?php endif; ?>
        <?php for($i=max(1,$currentPage-2);$i<=min($totalPages,$currentPage+2);$i++): ?>
          <?php if($i===$currentPage): ?><span class="is-active"><?= $i ?></span><?php else: ?><form class="pagination-form" method="post" action="<?= assets('search/'.$i) ?>"><input type="hidden" name="search" value="<?= htmlspecialchars($query,ENT_QUOTES,'UTF-8') ?>"><button type="submit"><?= $i ?></button></form><?php endif; ?>
        <?php endfor; ?>
        <?php if ($currentPage < $totalPages): ?><form class="pagination-form" method="post" action="<?= assets('search/'.($currentPage+1)) ?>"><input type="hidden" name="search" value="<?= htmlspecialchars($query,ENT_QUOTES,'UTF-8') ?>"><button type="submit">بعدی</button></form><?php else: ?><span class="is-disabled">بعدی</span><?php endif; ?>
      </nav><?php endif; ?>
    <?php else: ?>
      <div class="empty-state"><div class="empty-state__icon">⌕</div><h2>نتیجه‌ای پیدا نشد</h2><p>عبارت دیگری را امتحان کنید یا از فهرست شهرها و دسته‌بندی‌ها استفاده کنید.</p></div>
    <?php endif; ?>
  </div></section>
</main>
<?php require BASE_PATH . '/them/app/layout/footer.php'; ?>
<?php require BASE_PATH . '/them/app/layout/js.php'; ?>
</body>
</html>