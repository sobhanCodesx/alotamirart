<?php
/**
 * Central database configuration.
 *
 * On shared hosting you normally only edit this file.
 * Environment variables override these defaults when the host supports them.
 */
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'name' => getenv('DB_NAME') ?: 'danesh',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
    'charset' => 'utf8mb4',
];
