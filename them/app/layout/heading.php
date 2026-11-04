<?php
$siteCssVersion = @filemtime(BASE_PATH . '/public/src/css/site.css') ?: '1';
?>
<link rel="stylesheet" href="<?= assets('public/src/css/bootstrap.rtl.min.css') ?>">
<link rel="stylesheet" href="<?= assets('public/src/css/site.css') ?>?v=<?= urlencode((string)$siteCssVersion) ?>">
<?php $aloExploreCssVersion = @filemtime(BASE_PATH . '/public/src/css/alo-explore.css') ?: '1'; ?>
<link rel="stylesheet" href="<?= assets('public/src/css/alo-explore.css') ?>?v=<?= urlencode((string)$aloExploreCssVersion) ?>">
<link rel="icon" type="image/x-icon" href="<?= assets(isset($dataSeo['logo']) ? $dataSeo['logo'] : 'public/src/img/logo.png') ?>">
<link rel="apple-touch-icon" href="<?= assets(isset($dataSeo['logo']) && $dataSeo['logo'] !== '' ? $dataSeo['logo'] : 'public/src/img/logo.png') ?>">
<meta name="theme-color" content="#172554">