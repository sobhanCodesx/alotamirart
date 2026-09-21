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
$dbConfig = Config::load('database');

date_default_timezone_set(isset($appConfig['timezone']) ? $appConfig['timezone'] : 'Asia/Tehran');

if (!defined('CURRENT_DOMAIN')) define('CURRENT_DOMAIN', rtrim(currentdomain(), '/') . '/');
if (!defined('DISPLAY_ERROR')) define('DISPLAY_ERROR', !empty($appConfig['debug']));
if (!defined('DB_HOST')) define('DB_HOST', isset($dbConfig['host']) ? $dbConfig['host'] : 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', isset($dbConfig['name']) ? $dbConfig['name'] : '');
if (!defined('BD_NAME')) define('BD_NAME', DB_NAME);
if (!defined('DB_USERNAME')) define('DB_USERNAME', isset($dbConfig['username']) ? $dbConfig['username'] : '');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', isset($dbConfig['password']) ? $dbConfig['password'] : '');

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

$container->singleton(Database::class, function () use ($dbConfig) { return new Database($dbConfig); });
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

$publicControllers = [
    App\Http\Controllers\HomeController::class,
    App\Http\Controllers\PostController::class,
    App\Http\Controllers\BrandController::class,
    App\Http\Controllers\CityController::class,
    App\Http\Controllers\SitemapController::class,
];
foreach ($publicControllers as $class) {
    $container->singleton($class, function ($c) use ($class) {
        return new $class(
            $c->get(Request::class),
            $c->get(View::class),
            $c->get(SiteContext::class),
            $c->get(UploadService::class),
            $c->get(Database::class)
        );
    });
}

$container->singleton(App\Http\Controllers\AuthController::class, function ($c) {
    return new App\Http\Controllers\AuthController(
        $c->get(Request::class),
        $c->get(View::class),
        $c->get(SiteContext::class),
        $c->get(UploadService::class),
        $c->get(Database::class),
        $c->get(AuthService::class)
    );
});

$adminControllers = [
    App\Http\Admin\DashboardController::class,
    App\Http\Admin\MenuController::class,
    App\Http\Admin\PostController::class,
    App\Http\Admin\UserController::class,
    App\Http\Admin\BrandController::class,
    App\Http\Admin\BrandPostController::class,
    App\Http\Admin\LinkController::class,
    App\Http\Admin\SettingsController::class,
];
foreach ($adminControllers as $class) {
    $container->singleton($class, function ($c) use ($class) {
        return new $class(
            $c->get(Request::class),
            $c->get(View::class),
            $c->get(SiteContext::class),
            $c->get(UploadService::class),
            $c->get(Database::class),
            $c->get(AuthService::class)
        );
    });
}

$panelControllers = [
    App\Http\Panel\DashboardController::class,
    App\Http\Panel\PostController::class,
    App\Http\Panel\BrandController::class,
];
foreach ($panelControllers as $class) {
    $container->singleton($class, function ($c) use ($class) {
        return new $class(
            $c->get(Request::class),
            $c->get(View::class),
            $c->get(SiteContext::class),
            $c->get(UploadService::class),
            $c->get(Database::class),
            $c->get(AuthService::class)
        );
    });
}

$router = new Router($container, $request);
require BASE_PATH . '/routes/admin.php';
require BASE_PATH . '/routes/panel.php';
require BASE_PATH . '/routes/web.php';

return new Application(
    $container,
    $router,
    $container->get(Logger::class),
    !empty($appConfig['debug'])
);
