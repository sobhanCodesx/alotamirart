<?php
$siteJsVersion = @filemtime(BASE_PATH . '/public/src/js/site.js') ?: '1';
?>
<script src="<?= assets('public/src/js/bootstrap.min.js') ?>" defer></script>
<script src="<?= assets('public/src/js/site.js') ?>?v=<?= urlencode((string)$siteJsVersion) ?>" defer></script>