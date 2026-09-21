<?php

/**
 * Central database configuration.
 *
 * Set database connection values only in this file.
 * Environment variables can override the local defaults.
 */
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'name' => getenv('DB_NAME') ?: 'danesh',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
];
