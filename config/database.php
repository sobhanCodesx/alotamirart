<?php

/**
 * Single source of database configuration.
 *
 * Local development is detected automatically from the HTTP host.
 * Production keeps the historical connection values unchanged.
 */

$httpHost = isset($_SERVER['HTTP_HOST'])
    ? strtolower((string) $_SERVER['HTTP_HOST'])
    : (isset($_SERVER['SERVER_NAME']) ? strtolower((string) $_SERVER['SERVER_NAME']) : '');

$hostName = preg_replace('/:\\d+$/', '', $httpHost);
$isLocal = in_array($hostName, ['localhost', '127.0.0.1', '::1'], true)
    || getenv('APP_ENV') === 'local';

$local = [
    'host' => 'localhost',
    'name' => 'danesh',
    'username' => 'root',
    'password' => '',
];

$productionPrimary = [
    'host' => 'localhost',
    'name' => 'mbziliwc_danesh',
    'username' => 'mbziliwc_danesh',
    'password' => '9711212103',
];

$productionLegacyConstants = [
    'host' => 'localhost',
    'name' => 'mbziliwc_danesh',
    'username' => 'mbziliwc_danesh',
    'password' => 'fV7+Qjy[RU5S',
];

return [
    'environment' => $isLocal ? 'local' : 'production',
    'primary' => $isLocal ? $local : $productionPrimary,
    'legacy_constants' => $isLocal ? $local : $productionLegacyConstants,
];
