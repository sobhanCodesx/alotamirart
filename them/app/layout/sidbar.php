<div class="sidebar-stack">
  <aside class="sidebar-box">
    <h2>آخرین مقالات</h2>
    <div class="sidebar-list">
      <?php if (!empty($side_post) && is_array($side_post)): ?>
        <?php foreach ($side_post as $pu): ?>
          <a class="sidebar-item" href="<?= assets('post/' . (int)$pu['id']) ?>">
            <img src="<?= assets($pu['img']) ?>" alt="<?= htmlspecialchars(clean_display_text(isset($pu['title']) ? $pu['title'] : ''), ENT_QUOTES, 'UTF-8') ?>" width="72" height="64" loading="lazy" decoding="async">
            <span>
              <strong><?= htmlspecialchars(excerpt_text(isset($pu['title']) ? $pu['title'] : '', 7), ENT_QUOTES, 'UTF-8') ?></strong>
              <span><?= htmlspecialchars(excerpt_text(isset($pu['content']) ? $pu['content'] : '', 10), ENT_QUOTES, 'UTF-8') ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </aside>

  <aside class="sidebar-box">
    <h2>مطالب برندها</h2>
    <div class="sidebar-list">
      <?php if (!empty($side_brand) && is_array($side_brand)): ?>
        <?php foreach ($side_brand as $pu): ?>
          <a class="sidebar-item" href="<?= assets((isset($pu['slug']) ? $pu['slug'] : '') . '/' . (int)$pu['id']) ?>">
            <img src="<?= assets($pu['img']) ?>" alt="<?= htmlspecialchars(clean_display_text(isset($pu['title']) ? $pu['title'] : ''), ENT_QUOTES, 'UTF-8') ?>" width="72" height="64" loading="lazy" decoding="async">
            <span>
              <strong><?= htmlspecialchars(excerpt_text(isset($pu['title']) ? $pu['title'] : '', 7), ENT_QUOTES, 'UTF-8') ?></strong>
              <span><?= htmlspecialchars(excerpt_text(isset($pu['content']) ? $pu['content'] : '', 10), ENT_QUOTES, 'UTF-8') ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </aside>
</div>