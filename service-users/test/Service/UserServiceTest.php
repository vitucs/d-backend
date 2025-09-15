<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

use App\Model\User;
use App\Repository\UserRepository;
use App\Service\Client\WalletServiceClient;
use App\Service\UserService;
use Mockery;
use HyperfTest\BaseTestCase;
use Hyperf\Database\ConnectionResolverInterface;
use Hyperf\Database\ConnectionInterface;

class UserServiceTest extends BaseTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testRegisterNewUserSuccess(): void
    {
        $connectionMock = Mockery::mock(ConnectionInterface::class);
        $connectionMock->shouldReceive('beginTransaction')->once();
        $connectionMock->shouldReceive('commit')->once();
        $connectionMock->shouldReceive('rollBack')->never();

        $connectionResolverMock = Mockery::mock(ConnectionResolverInterface::class);
        $connectionResolverMock->shouldReceive('connection')->andReturn($connectionMock);

        $userData = [
            'id' => 1,
            'full_name' => 'Test User',
            'document'  => '12345678901',
            'email'     => 'test@example.com',
            'password'  => 'password123',
            'type'      => 'common',
        ];

        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->id = 1;

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('createUser')->once()->with($userData)->andReturn($mockUser);

        $walletServiceClientMock = Mockery::mock(WalletServiceClient::class);
        $walletServiceClientMock->shouldReceive('createWalletForUser')->once()->with(1)->andReturnTrue();

        $userService = new UserService($userRepositoryMock, $walletServiceClientMock, $connectionResolverMock);
        
        $result = $userService->registerNewUser($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
    }
}
