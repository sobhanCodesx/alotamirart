<?php

function bootstrapAssert($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "Bootstrap contract failed: " . $message . PHP_EOL);
        exit(1);
    }
}

$root = dirname(__DIR__);
$index = file_get_contents($root . '/index.php');

$positions = [
    'admin' => strpos($index, "require_once './classes/admin/Admin.php';"),
    'adminCities' => strpos($index, "require_once './classes/admin/cities/AdminCities.php';"),
    'panel' => strpos($index, "require_once './classes/panel/Panel.php';"),
    'postUser' => strpos($index, "require_once './classes/panel/PostUser.php';"),
    'brandUser' => strpos($index, "require_once './classes/panel/BrandUser.php';"),
];

foreach ($positions as $name => $position) {
    bootstrapAssert($position !== false, $name . ' require is missing');
}

bootstrapAssert($positions['admin'] < $positions['adminCities'], 'Admin must load before AdminCities');
bootstrapAssert($positions['panel'] < $positions['postUser'], 'Panel must load before PostUser');
bootstrapAssert($positions['panel'] < $positions['brandUser'], 'Panel must load before BrandUser');
bootstrapAssert(substr_count($index, "classes/admin/brand/BrandPosts.php") === 1, 'BrandPosts must be required exactly once');

echo "Legacy bootstrap contract passed" . PHP_EOL;
