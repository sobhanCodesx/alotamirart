<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$serviceRoutes = [
    '#^/refrigerator-repair-in-([a-zA-Z0-9-]+)$#' => 'refrigerator',
    '#^/washing-machine-repair-in-([a-zA-Z0-9-]+)$#' => 'washing-machine',
    '#^/air-conditioner-repair-in-([a-zA-Z0-9-]+)$#' => 'air-conditioner',
    '#^/tv-repair-in-([a-zA-Z0-9-]+)$#' => 'tv',
    '#^/oven-repair-in-([a-zA-Z0-9-]+)$#' => 'oven',
    '#^/dishwasher-repair-in-([a-zA-Z0-9-]+)$#' => 'dishwasher',
];

foreach ($serviceRoutes as $pattern => $service) {
    if (preg_match($pattern, $path, $matches)) {
        $_GET['service'] = $service;
        $_GET['city'] = $matches[1];
        require __DIR__ . '/service-city.php';
        return true;
    }
}

if (preg_match('#^/services-in-([a-zA-Z0-9-]+)$#', $path, $matches)) {
    $_GET['city'] = $matches[1];
    require __DIR__ . '/show-city.php';
    return true;
}

$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
return true;
