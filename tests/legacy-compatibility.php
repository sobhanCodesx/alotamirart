<?php

function compatAssert($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "Compatibility check failed: " . $message . PHP_EOL);
        exit(1);
    }
}

$root = dirname(__DIR__);
$index = file_get_contents($root . '/index.php');
$htaccess = file_get_contents($root . '/.htaccess');
$database = file_get_contents($root . '/database/DataBase.php');
$originalHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

$_SERVER['HTTP_HOST'] = 'localhost:8080';
$localConfig = require $root . '/config/database.php';

compatAssert($localConfig['environment'] === 'local', 'localhost must select local DB configuration');
compatAssert($localConfig['primary']['host'] === 'localhost', 'local DB host must be localhost');
compatAssert($localConfig['primary']['name'] === 'danesh', 'local DB name must be danesh');
compatAssert($localConfig['primary']['username'] === 'root', 'local DB username must be root');
compatAssert($localConfig['primary']['password'] === '', 'local DB password must be empty');
compatAssert($localConfig['legacy_constants'] === $localConfig['primary'], 'legacy constants must use local DB in local development');

$_SERVER['HTTP_HOST'] = 'example.com';
$config = require $root . '/config/database.php';
compatAssert($config['environment'] === 'production', 'non-local host must preserve production DB configuration');

if ($originalHost === null) {
    unset($_SERVER['HTTP_HOST']);
} else {
    $_SERVER['HTTP_HOST'] = $originalHost;
}

compatAssert(strpos($index, "require_once './router/admin.php';") !== false, 'legacy admin router must remain active');
compatAssert(strpos($index, "require_once './router/panel.php';") !== false, 'legacy panel router must remain active');
compatAssert(strpos($index, "require_once './router/them.php';") !== false, 'legacy public router must remain active');
compatAssert(strpos($index, "require_once './classes/app/Auth.php';") !== false, 'legacy auth class must remain active');

compatAssert(strpos($htaccess, 'service-city.php?service=refrigerator&city=$1') !== false, 'legacy refrigerator URL rewrite must remain');
compatAssert(strpos($htaccess, 'service-city.php?service=washing-machine&city=$1') !== false, 'legacy washing-machine URL rewrite must remain');
compatAssert(strpos($htaccess, 'show-city.php?city=$1') !== false, 'legacy city-service URL rewrite must remain');

foreach (['select', 'selectOne', 'selectAll', 'new_select', 'all', 'insert', 'update', 'delete', 'query', 'getLastInsert', 'count'] as $method) {
    compatAssert(strpos($database, 'function ' . $method . '(') !== false, 'legacy DataBase method missing: ' . $method);
}
compatAssert(strpos($database, '$db = new DataBase();') !== false, 'legacy global database instance must remain');

compatAssert(isset($config['primary']['host'], $config['primary']['name'], $config['primary']['username'], $config['primary']['password']), 'primary DB configuration must be complete');
compatAssert(isset($config['legacy_constants']['host'], $config['legacy_constants']['name'], $config['legacy_constants']['username'], $config['legacy_constants']['password']), 'legacy DB constants compatibility must be preserved');

foreach (['city-services.php', 'service-city.php', 'service-washing-machine.php', 'show-city.php'] as $file) {
    $content = file_get_contents($root . '/' . $file);
    compatAssert(strpos($content, "require __DIR__ . '/config/database.php'") !== false, $file . ' must use central database config');
}

echo "Legacy compatibility contract passed" . PHP_EOL;
