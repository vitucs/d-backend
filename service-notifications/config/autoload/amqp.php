<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    'default' => [
        'host' => env('RABBITMQ_HOST', 'rabbitmq'),
        'port' => (int) env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', 'user'),
        'password' => env('RABBITMQ_PASS', 'password'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'consumers' => [],
        'producers' => [],
    ],
];