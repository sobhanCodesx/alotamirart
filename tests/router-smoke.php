<?php
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Core/Container.php';
require BASE_PATH . '/app/Core/Request.php';
require BASE_PATH . '/app/Core/Router.php';

use App\Core\Container;
use App\Core\Request;
use App\Core\Router;

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual: ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$container = new Container();

$request = new Request('GET', '/services-in-tehran', [], [], [], []);
$router = new Router($container, $request);
$router->get('services-in-{city}', function ($city) { return $city; });
assertSameValue('tehran', $router->dispatch(), 'Embedded route parameter failed.');

$request = new Request('GET', '/post/12/test-slug', [], [], [], []);
$router = new Router($container, $request);
$router->get('post/{id}/{slug}', function ($id, $slug) { return $id . '|' . $slug; });
assertSameValue('12|test-slug', $router->dispatch(), 'Multi-parameter route failed.');

$request = new Request('GET', '/', [], [], [], []);
$router = new Router($container, $request);
$router->get('/', function () { return 'home'; });
assertSameValue('home', $router->dispatch(), 'Root route failed.');

echo "Router smoke tests passed\n";
