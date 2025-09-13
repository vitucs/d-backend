<?php

declare(strict_types=1);

return [
    'default' => [
        'driver' => Hyperf\AsyncQueue\Driver\RedisDriver::class,
        'redis' => [
            'pool' => 'default'
        ],
        'channel' => 'notifications',
        'timeout' => 2,
        'retry_seconds' => [5, 15, 30],
        'handle_timeout' => 10,
        'max_attempts' => 3,
        'processes' => 2,
    ],
];