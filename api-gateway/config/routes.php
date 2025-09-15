<?php
declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

Router::post('/users', 'App\Controller\UserController@create');
Router::post('/users/{id}/balance', 'App\Controller\UserController@addBalance');

Router::post('/transfer', 'App\Controller\TransactionController@create');