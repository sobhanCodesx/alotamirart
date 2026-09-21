<?php
define('BASE_PATH', dirname(__DIR__));
define('APP_BOOTSTRAPPED', true);

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;
    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

require_once BASE_PATH . '/app/Support/helpers.php';

use App\Core\Application;
use App\Core\Config;
use App\Core\Container;
use App\Core\Database;
use App\Core\FileCache;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Router;
use App\Core\View;
use App\Services\AuthService;
use App\Services\SiteContext;
use App\Services\UploadService;

$appConfig = Config::load('app');
date_default_timezone_set(isset($appConfig['timezone']) ? $appConfig['timezone'] : 'Asia/Tehran');

if (!empty($appConfig['debug'])) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    if (!empty($appConfig['session']['name'])) session_name($appConfig['session']['name']);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => protocol() === 'https://',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$container = new Container();
$request = Request::capture();
$container->instance(Request::class, $request);

$container->singleton(Database::class, function () { return new Database(Config::load('database')); });
$container->singleton(FileCache::class, function () use ($appConfig) {
    $cache = isset($appConfig['cache']) ? $appConfig['cache'] : [];
    return new FileCache(
        isset($cache['path']) ? $cache['path'] : BASE_PATH . '/storage/cache',
        !isset($cache['enabled']) || $cache['enabled']
    );
});
$container->singleton(View::class, function () { return new View(BASE_PATH); });
$container->singleton(Logger::class, function () { return new Logger(BASE_PATH . '/storage/logs/app.log'); });
$container->singleton(SiteContext::class, function ($c) { return new SiteContext($c->get(Database::class), $c->get(FileCache::class)); });
$container->singleton(AuthService::class, function ($c) { return new AuthService($c->get(Database::class)); });
$container->singleton(UploadService::class, function () { return new UploadService(BASE_PATH); });

$router = new Router($container, $request);

return new Application(
    $container,
    $router,
    $container->get(Logger::class),
    !empty($appConfig['debug'])
);
