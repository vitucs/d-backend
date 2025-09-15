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
use Hyperf\HttpServer\Router\Router;

Router::post('/users', 'App\Controller\UserController@create');

Router::get('/users/{id}', 'App\Controller\UserController@show');

Router::post('/users/{id}/balance', 'App\Controller\UserController@addBalance');