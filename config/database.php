<?php

/**
 * Single source of database configuration.
 *
 * The primary section preserves the exact connection values used by the
 * production code before the architecture refactor.
 *
 * legacy_constants exists only because the historical index.php exposed a
 * slightly different DB_* constant set. Keeping it prevents an accidental
 * backward-compatibility break while the project is migrated gradually.
 */
return [
    'primary' => [
        'host' => 'localhost',
        'name' => 'mbziliwc_danesh',
        'username' => 'mbziliwc_danesh',
        'password' => '9711212103',
    ],
    'legacy_constants' => [
        'host' => 'localhost',
        'name' => 'mbziliwc_danesh',
        'username' => 'mbziliwc_danesh',
        'password' => 'fV7+Qjy[RU5S',
    ],
];
