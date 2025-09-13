<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use function Hyperf\Support\env;

return [
    'users_service_url' => env('USERS_SERVICE_URL', 'http://service-users:9501'),
    'authorization_service_url' => env('AUTHORIZATION_SERVICE_URL', 'https://util.devi.tools/api/v2/authorize'),
];
