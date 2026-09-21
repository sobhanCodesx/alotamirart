<?php

function routeAssert($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "Route contract failed: " . $message . PHP_EOL);
        exit(1);
    }
}

$root = dirname(__DIR__);
require_once $root . '/router/LegacyRouteMatcher.php';

routeAssert(LegacyRouteMatcher::match('/', '/') === [], 'root route must match');
routeAssert(LegacyRouteMatcher::match('services-in-{city}', '/services-in-tehran') === ['tehran'], 'embedded city parameter must match');
routeAssert(LegacyRouteMatcher::match('refrigerator-repair-in-{city}', '/refrigerator-repair-in-karaj') === ['karaj'], 'embedded service route must match');
routeAssert(LegacyRouteMatcher::match('post/{id}/{slug}', '/post/12/test-slug') === ['12', 'test-slug'], 'multi parameter route must match');
routeAssert(LegacyRouteMatcher::match('login', '/logout') === null, 'unrelated route must not match');

$classFiles = [
    'Dashboard' => 'classes/admin/index.php',
    'AdminCities' => 'classes/admin/cities/AdminCities.php',
    'Menu' => 'classes/admin/menu/Menu.php',
    'Post' => 'classes/admin/posts/Post.php',
    'User' => 'classes/admin/users/User.php',
    'Items' => 'classes/admin/brand/Items.php',
    'BrandPosts' => 'classes/admin/brand/BrandPosts.php',
    'Link' => 'classes/admin/link.php',
    'Seo' => 'classes/admin/settings/Seo.php',
    'Header' => 'classes/admin/settings/Header.php',
    'Footer' => 'classes/admin/settings/Footer.php',
    'Panel' => 'classes/panel/Panel.php',
    'PostUser' => 'classes/panel/PostUser.php',
    'BrandUser' => 'classes/panel/BrandUser.php',
    'Hom' => 'classes/app/Hom.php',
    'Posts' => 'classes/app/Posts.php',
    'Cities' => 'classes/app/Cities.php',
    'Brands' => 'classes/app/Brands.php',
    'SiteMap' => 'classes/app/SiteMap.php',
    'Auth' => 'classes/app/Auth.php',
];

function parseRoutes($source, $routerFile)
{
    $pattern = '/uri\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*[\'"]([^\'"]+)[\'"])?\s*\)/';
    preg_match_all($pattern, $source, $matches, PREG_SET_ORDER);

    $routes = [];
    foreach ($matches as $match) {
        $routes[] = [
            'path' => $match[1],
            'class' => $match[2],
            'method' => $match[3],
            'verb' => strtoupper(isset($match[4]) && $match[4] !== '' ? $match[4] : 'GET'),
            'file' => $routerFile,
        ];
    }
    return $routes;
}

function methodArity($source, $method)
{
    $pattern = '/function\s+' . preg_quote($method, '/') . '\s*\(([^)]*)\)/s';
    if (!preg_match($pattern, $source, $match)) {
        return null;
    }

    $params = trim($match[1]);
    if ($params === '') {
        return [0, 0];
    }

    $parts = array_map('trim', explode(',', $params));
    $required = 0;
    foreach ($parts as $part) {
        if (strpos($part, '=') === false) {
            $required++;
        }
    }

    return [$required, count($parts)];
}

function routeRegex($path)
{
    $parts = preg_split('/(\{[A-Za-z_][A-Za-z0-9_]*\})/', trim($path, '/ '), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $regex = '';
    foreach ($parts as $part) {
        $regex .= preg_match('/^\{[A-Za-z_][A-Za-z0-9_]*\}$/', $part)
            ? '[^/]+'
            : preg_quote($part, '#');
    }
    return '#^' . $regex . '$#u';
}

function sampleRoute($path)
{
    return preg_replace('/\{[A-Za-z_][A-Za-z0-9_]*\}/', 'sample', trim($path, '/ '));
}

function routeSpecificity($path)
{
    return strlen(preg_replace('/\{[A-Za-z_][A-Za-z0-9_]*\}/', '', trim($path, '/ ')));
}

$routes = [];
foreach (['router/admin.php', 'router/panel.php', 'router/them.php'] as $routerFile) {
    $routes = array_merge($routes, parseRoutes(file_get_contents($root . '/' . $routerFile), $routerFile));
}

$seen = [];
$classSource = [];
foreach ($routes as $route) {
    $key = $route['verb'] . ' ' . trim($route['path'], '/ ');
    routeAssert(!isset($seen[$key]), 'duplicate route: ' . $key . ' in ' . $route['file']);
    $seen[$key] = true;

    routeAssert(isset($classFiles[$route['class']]), 'unknown route class ' . $route['class'] . ' for ' . $key);
    $classFile = $root . '/' . $classFiles[$route['class']];
    routeAssert(is_file($classFile), 'class file missing for ' . $route['class']);

    if (!isset($classSource[$route['class']])) {
        $classSource[$route['class']] = file_get_contents($classFile);
    }

    $arity = methodArity($classSource[$route['class']], $route['method']);
    routeAssert($arity !== null, 'missing target ' . $route['class'] . '::' . $route['method'] . ' for ' . $key);

    preg_match_all('/\{[A-Za-z_][A-Za-z0-9_]*\}/', $route['path'], $placeholderMatches);
    $provided = count($placeholderMatches[0]) + ($route['verb'] === 'POST' ? 1 : 0);
    routeAssert(
        $provided >= $arity[0] && $provided <= $arity[1],
        'argument count mismatch for ' . $route['class'] . '::' . $route['method'] . ' on ' . $key
    );
}

for ($i = 0, $count = count($routes); $i < $count; $i++) {
    for ($j = $i + 1; $j < $count; $j++) {
        if ($routes[$i]['verb'] !== $routes[$j]['verb']) continue;
        if ($routes[$i]['file'] !== $routes[$j]['file']) continue;
        if (routeSpecificity($routes[$i]['path']) >= routeSpecificity($routes[$j]['path'])) continue;

        if (preg_match(routeRegex($routes[$i]['path']), sampleRoute($routes[$j]['path']))) {
            routeAssert(false, 'route ' . $routes[$i]['path'] . ' shadows later route ' . $routes[$j]['path'] . ' in ' . $routes[$i]['file']);
        }
    }
}

echo "Legacy route contract passed" . PHP_EOL;
