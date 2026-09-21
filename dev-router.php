<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$legacyFrontControllerEndpoints = [
    '/service-city.php',
    '/show-city.php',
    '/city-services.php',
    '/service-washing-machine.php',
];

if (in_array($path, $legacyFrontControllerEndpoints, true)) {
    require __DIR__ . '/index.php';
    return true;
}

$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
