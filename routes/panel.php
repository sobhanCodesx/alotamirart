<?php
use App\Http\Panel\BrandController;
use App\Http\Panel\DashboardController;
use App\Http\Panel\PostController;

$router->get('panelcp', [DashboardController::class, 'index']);
$router->post('update/profile/{id}', [DashboardController::class, 'update']);

$router->get('user/post/{page}', [PostController::class, 'index']);
$router->get('user/posts/create', [PostController::class, 'create']);
$router->post('user/post/created', [PostController::class, 'store']);
$router->get('user/post/update/{id}', [PostController::class, 'edit']);
$router->post('userpost/updated/{id}', [PostController::class, 'update']);

$router->get('user/brand/{page}', [BrandController::class, 'index']);
$router->get('userbrand/create', [BrandController::class, 'create']);
$router->post('user/brand/created', [BrandController::class, 'store']);
$router->get('user/brand/update/{id}', [BrandController::class, 'edit']);
$router->post('userbrand/updated/{id}', [BrandController::class, 'update']);
$router->post('user/brand/update/{id}', [BrandController::class, 'update']);
