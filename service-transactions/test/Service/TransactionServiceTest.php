<?php

declare(strict_types=1);

namespace HyperfTest\Service;

use App\Exception\TransactionException;
use App\Job\NotificationJob;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use App\Service\Client\AuthorizationServiceClient;
use App\Service\Client\UserServiceClient;
use App\Service\TransactionService;
use App\ValueObject\User;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\Context\ApplicationContext;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSource;
use Hyperf\Testing\TestCase;
use Mockery;

class TransactionServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testHandleTransferSuccess()
    {
        $container = new Container(new DefinitionSource([]));
        ApplicationContext::setContainer($container);

        $payer = new User(['id' => 1, 'email' => 'payer@test.com', 'type' => 'common', 'full_name' => 'Payer User']);
        $payee = new User(['id' => 2, 'email' => 'payee@test.com', 'type' => 'common', 'full_name' => 'Payee User']);
        $payerWallet = (object) ['balance' => 200.00, 'user_id' => 1];

        $userServiceClient = Mockery::mock(UserServiceClient::class);
        $userServiceClient->shouldReceive('findUserById')->with(1)->andReturn($payer);
        $userServiceClient->shouldReceive('findUserById')->with(2)->andReturn($payee);

        $authServiceClient = Mockery::mock(AuthorizationServiceClient::class);
        $authServiceClient->shouldReceive('isAuthorized')->once()->andReturn(true);

        $walletRepository = Mockery::mock(WalletRepository::class);
        $walletRepository->shouldReceive('findByUserId')->with(1)->andReturn($payerWallet);
        $walletRepository->shouldReceive('decrementBalance')->andReturn(true);
        $walletRepository->shouldReceive('incrementBalance')->andReturn(true);
        $walletRepository->shouldReceive('findByUserId')->with(2)->andReturn((object)['user_id' => 2]);


        $transactionRepository = Mockery::mock(TransactionRepository::class);
        $transactionRepository->shouldReceive('logTransaction')->once();

        Db::shouldReceive('beginTransaction')->once();
        Db::shouldReceive('commit')->once();
        Db::shouldReceive('rollBack')->never();

        $queueDriver = Mockery::mock(DriverInterface::class);
        $queueDriver->shouldReceive('push')->with(Mockery::on(function ($job) {
            return $job instanceof NotificationJob && $job->amount === 100.0;
        }))->once();
        $driverFactory = Mockery::mock(DriverFactory::class);
        $driverFactory->shouldReceive('get')->with('default')->andReturn($queueDriver);

        $container->set(UserServiceClient::class, $userServiceClient);
        $container->set(AuthorizationServiceClient::class, $authServiceClient);
        $container->set(WalletRepository::class, $walletRepository);
        $container->set(TransactionRepository::class, $transactionRepository);
        $container->set(DriverFactory::class, $driverFactory);

        $transactionService = $container->get(TransactionService::class);
        $transactionService->handleTransfer(1, 2, 100.0);

        $this->assertTrue(true);
    }

    public function testHandleTransferFailsIfPayerIsShopkeeper()
    {
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Lojistas não podem realizar transferências.');
        $this->expectExceptionCode(403);

        $container = new Container(new DefinitionSource([]));
        ApplicationContext::setContainer($container);

        $payer = new User(['id' => 1, 'email' => 'payer@test.com', 'type' => 'shopkeeper']);
        $payee = new User(['id' => 2, 'email' => 'payee@test.com', 'type' => 'common']);
        $payerWallet = (object) ['balance' => 200.00, 'user_id' => 1];


        $userServiceClient = Mockery::mock(UserServiceClient::class);
        $userServiceClient->shouldReceive('findUserById')->with(1)->andReturn($payer);
        $userServiceClient->shouldReceive('findUserById')->with(2)->andReturn($payee);

        $walletRepository = Mockery::mock(WalletRepository::class);
        $walletRepository->shouldReceive('findByUserId')->with(1)->andReturn($payerWallet);

        $container->set(UserServiceClient::class, $userServiceClient);
        $container->set(WalletRepository::class, $walletRepository);

        $container->set(AuthorizationServiceClient::class, Mockery::mock(AuthorizationServiceClient::class));
        $container->set(TransactionRepository::class, Mockery::mock(TransactionRepository::class));
        $container->set(DriverFactory::class, Mockery::mock(DriverFactory::class));
        
        $transactionService = $container->get(TransactionService::class);
        $transactionService->handleTransfer(1, 2, 100.0);
    }
}