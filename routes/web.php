<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;

$router->get('/', [HomeController::class, 'index']);
$router->get('home', [HomeController::class, 'index']);

$router->get('posts/categories/{id}/{page}', [PostController::class, 'category']);
$router->get('post/{id}', [PostController::class, 'show']);
$router->get('post/{id}/{slug}', [PostController::class, 'show']);

$router->get('cities', [CityController::class, 'index']);
$router->get('city/{slug}', [CityController::class, 'show']);
$router->get('services-in-{city}', [CityController::class, 'services']);

$router->get('refrigerator-repair-in-{city}', function ($city) use ($container) {
    return $container->get(CityController::class)->serviceDetail('refrigerator', $city);
});
$router->get('washing-machine-repair-in-{city}', function ($city) use ($container) {
    return $container->get(CityController::class)->serviceDetail('washing-machine', $city);
});
$router->get('air-conditioner-repair-in-{city}', function ($city) use ($container) {
    return $container->get(CityController::class)->serviceDetail('air-conditioner', $city);
});
$router->get('tv-repair-in-{city}', function ($city) use ($container) {
    return $container->get(CityController::class)->serviceDetail('tv', $city);
});
$router->get('oven-repair-in-{city}', function ($city) use ($container) {
    return $container->get(CityController::class)->serviceDetail('oven', $city);
});
$router->get('dishwasher-repair-in-{city}', function ($city) use ($container) {
    return $container->get(CityController::class)->serviceDetail('dishwasher', $city);
});

$router->get('brands/categories/{id}/{page}', [BrandController::class, 'category']);

$router->get('post/sitemap', [SitemapController::class, 'post']);
$router->get('brand/sitemap', [SitemapController::class, 'brand']);

$router->post('search/{page}', [HomeController::class, 'search']);

$router->get('register', [AuthController::class, 'register']);
$router->post('registered', [AuthController::class, 'registered']);
$router->get('login', [AuthController::class, 'login']);
$router->post('logined', [AuthController::class, 'logined']);
$router->get('logout', [AuthController::class, 'logout']);

$router->get('profile/{name}/{id}', [HomeController::class, 'profile']);
$router->get('menu/{slug}', [HomeController::class, 'menu']);

$router->get('show-city.php', [CityController::class, 'legacyServices']);
$router->get('city-services.php', [CityController::class, 'legacyServices']);
$router->get('service-city.php', [CityController::class, 'legacyService']);
$router->get('service-washing-machine.php', [CityController::class, 'washingMachine']);

$router->get('{slug}/{id}', [BrandController::class, 'show']);
