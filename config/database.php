<?php
/**
 * Central database configuration for both the legacy website and the MCP.
 * Values come from the server-only project-root .env, not committed secrets.
 */
require_once dirname(__DIR__) . '/app/Support/env.php';
aloLoadEnv(dirname(__DIR__) . '/.env');

$env = static function (string $key, string $fallback = ''): string {
    $value = getenv($key);
    return $value === false ? $fallback : (string) $value;
};

$httpHost = isset($_SERVER['HTTP_HOST'])
    ? strtolower((string) $_SERVER['HTTP_HOST'])
    : (isset($_SERVER['SERVER_NAME']) ? strtolower((string) $_SERVER['SERVER_NAME']) : '');

$hostName = preg_replace('/:\d+$/', '', $httpHost);
$isLocal = in_array($hostName, ['localhost', '127.0.0.1', '::1'], true)
    || $env('APP_ENV') === 'local';

$local = [
    'host' => $env('DB_HOST', 'localhost'),
    'name' => $env('DB_NAME', 'danesh'),
    'username' => $env('DB_USERNAME', 'root'),
    'password' => $env('DB_PASSWORD'),
];

$productionPrimary = [
    'host' => $env('DB_HOST', 'localhost'),
    'name' => $env('DB_NAME'),
    'username' => $env('DB_USERNAME'),
    'password' => $env('DB_PASSWORD'),
];

// Some legacy routes historically used a second set of DB credentials.
$productionLegacyConstants = [
    'host' => $env('DB_LEGACY_HOST', $productionPrimary['host']),
    'name' => $env('DB_LEGACY_NAME', $productionPrimary['name']),
    'username' => $env('DB_LEGACY_USERNAME', $productionPrimary['username']),
    'password' => $env('DB_LEGACY_PASSWORD', $productionPrimary['password']),
];

return [
    'environment' => $isLocal ? 'local' : 'production',
    'primary' => $isLocal ? $local : $productionPrimary,
    'legacy_constants' => $isLocal ? $local : $productionLegacyConstants,
];
