<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

use App\Model\User;
use App\Repository\UserRepository;
use App\Service\Client\WalletServiceClient;
use App\Service\UserService;
use Hyperf\DbConnection\Db;
use Mockery;
use PHPUnit\Framework\TestCase;
use Hyperf\Di\Container;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Config\Config;
use Hyperf\DbConnection\ConnectionResolver;
use Hyperf\Di\Definition\DefinitionSourceFactory;
use Hyperf\Database\ConnectionResolverInterface;
use Hyperf\Database\ConnectionInterface;
use Hyperf\Logger\LoggerFactory;


/**
 * @internal
 * @covers UserService
 */
class UserServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testRegisterNewUserSuccess(): void
    {
        // Configuração do Container e dependências simuladas
        $config = new Config([
            'databases' => require BASE_PATH . '/config/autoload/databases.php',
            'logger' => require BASE_PATH . '/config/autoload/logger.php',
        ]);

        $container = new Container((new DefinitionSourceFactory())());
        $container->set(ConfigInterface::class, $config);
        $container->set(LoggerFactory::class, new LoggerFactory($container, $config));

        $resolver = new ConnectionResolver($container);
        $container->set(ConnectionResolver::class, $resolver);
        $container->set(ConnectionResolverInterface::class, $resolver);
        $container->set(ConnectionInterface::class, $resolver->connection());

        ApplicationContext::setContainer($container);

        $userData = [
            'full_name' => 'Test User',
            'document'  => '12345678901',
            'email'     => 'test@example.com',
            'password'  => 'password123',
            'type'      => 'common',
        ];

        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->id = 1;

        $userRepositoryMock = Mockery::mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('createUser')->once()->andReturn($mockUser);

        $walletServiceClientMock = Mockery::mock(WalletServiceClient::class);
        $walletServiceClientMock->shouldReceive('createWalletForUser')->with(1)->once()->andReturn(true);

        $container->set(UserRepository::class, $userRepositoryMock);
        $container->set(WalletServiceClient::class, $walletServiceClientMock);

        $connectionMock = Mockery::mock(ConnectionInterface::class);
        $connectionMock->shouldReceive('beginTransaction')->once();
        $connectionMock->shouldReceive('commit')->once();
        $connectionMock->shouldReceive('rollBack')->never();

        $container->set(ConnectionInterface::class, $connectionMock);

        // Instanciando o serviço com container
        $userService = $container->get(UserService::class);

        // Executando o teste
        $result = $userService->registerNewUser($userData);

        // Asserts
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
    }
}
