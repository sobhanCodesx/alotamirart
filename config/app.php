<?php
return [
    'name' => 'Alo Tamir Art',
    'debug' => getenv('APP_DEBUG') === '1',
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Tehran',
    'cache' => [
        'enabled' => getenv('APP_CACHE') !== '0',
        'path' => BASE_PATH . '/storage/cache',
        'ttl' => 300,
    ],
    'session' => ['name' => 'alotamirart_session'],
];
