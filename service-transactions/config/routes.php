<?php
declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

// Rota para o api-gateway iniciar uma transferência
Router::post('/transfers', 'App\Controller\TransactionController@createTransfer');

// Rota para o service-users criar uma nova carteira
Router::post('/wallets', 'App\Controller\TransactionController@createWallet');