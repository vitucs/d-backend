<?php
declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

Router::post('/users', 'App\Controller\UserController@create');