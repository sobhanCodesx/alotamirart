<?php
use App\Http\Admin\BrandController;
use App\Http\Admin\BrandPostController;
use App\Http\Admin\DashboardController;
use App\Http\Admin\LinkController;
use App\Http\Admin\MenuController;
use App\Http\Admin\PostController;
use App\Http\Admin\SettingsController;
use App\Http\Admin\UserController;

$router->get('admin/dashboard', [DashboardController::class, 'index']);

$router->get('admin/menu/index/{page}', [MenuController::class, 'index']);
$router->get('admin/menu/create', [MenuController::class, 'create']);
$router->post('admin/menu/created', [MenuController::class, 'store']);
$router->get('admin/menu/update/{id}', [MenuController::class, 'edit']);
$router->post('admin/menu/updated/{id}', [MenuController::class, 'update']);
$router->get('admin/menu/deleted/{id}', [MenuController::class, 'delete']);

$router->get('admin/posts/index/{page}', [PostController::class, 'index']);
$router->get('admin/posts/create', [PostController::class, 'create']);
$router->post('admin/posts/created', [PostController::class, 'store']);
$router->get('admin/posts/update/{id}', [PostController::class, 'edit']);
$router->post('admin/posts/updated/{id}', [PostController::class, 'update']);
$router->get('admin/posts/deleted/{id}', [PostController::class, 'delete']);
$router->get('change/status/{id}', [PostController::class, 'status']);

$router->get('admin/users/{page}', [UserController::class, 'index']);
$router->get('admin/users/status/{id}', [UserController::class, 'role']);
$router->get('admin/users/delete/{id}', [UserController::class, 'delete']);
$router->get('user/status/{id}', [UserController::class, 'writer']);
$router->get('inner/user/{id}', [UserController::class, 'impersonate']);

$router->get('admin/brands/index/{page}', [BrandController::class, 'index']);
$router->post('admin/brands/created', [BrandController::class, 'store']);
$router->get('admin/brands/update/{id}', [BrandController::class, 'edit']);
$router->post('admin/brands/updated/{id}', [BrandController::class, 'update']);
$router->get('admin/brands/delete/{id}', [BrandController::class, 'delete']);

$router->get('admin/brands/post/{page}', [BrandPostController::class, 'index']);
$router->get('admin/brands/blog/create', [BrandPostController::class, 'create']);
$router->post('admin/brands/blogs/created', [BrandPostController::class, 'store']);
$router->get('admin/brands/post/update/{id}', [BrandPostController::class, 'edit']);
$router->post('admin/brands/post/updated/{id}', [BrandPostController::class, 'update']);
$router->get('admin/brands/post/delete/{id}', [BrandPostController::class, 'delete']);
$router->get('status/brand/{id}', [BrandPostController::class, 'status']);

$router->get('backlink/admin/{page}', [LinkController::class, 'index']);
$router->post('backlink/admin/create', [LinkController::class, 'store']);
$router->get('delete/backlink/{id}', [LinkController::class, 'delete']);

$router->get('admin/settings/seo', [SettingsController::class, 'seo']);
$router->post('admin/settings/seo/create', [SettingsController::class, 'saveSeo']);
$router->get('admin/settings/header', [SettingsController::class, 'header']);
$router->post('admin/settings/header/create', [SettingsController::class, 'saveHeader']);
$router->get('admin/settings/footer', [SettingsController::class, 'footer']);
$router->post('admin/settings/footer/create', [SettingsController::class, 'saveFooter']);
