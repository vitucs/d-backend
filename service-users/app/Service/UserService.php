<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepository;
use App\Service\Client\WalletServiceClient;
use Hyperf\DbConnection\Db;
use Throwable;
use App\Exception\BusinessException;
use Hyperf\Database\ConnectionResolverInterface;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private WalletServiceClient $walletServiceClient,
        private ConnectionResolverInterface $connectionResolver
    ) {
        $this->connectionResolver = $connectionResolver;
    }

    public function registerNewUser(array $data): User
    {
        $connection = $this->connectionResolver->connection();

        $connection->beginTransaction();
        try {
            $user = $this->userRepository->createUser($data);

            $walletCreated = $this->walletServiceClient->createWalletForUser($user->id);

            if (!$walletCreated) {
                throw new BusinessException(message: 'Falha no processo de criação da carteira do usuário.');
            }

            $connection->commit();

            return $user;
        } catch (Throwable $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public function findUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function addValueToWallet(array $data, int $id): User
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new BusinessException(404, 'Usuário não encontrado.');
        }

        $this->walletServiceClient->addBalanceToWallet($data['value'], $id);

        return $user;
    }
}