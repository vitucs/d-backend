<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepository;
use App\Service\Client\WalletServiceClient;
use Hyperf\DbConnection\Db;
use Throwable;
use App\Exception\BusinessException;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private WalletServiceClient $walletServiceClient
    ) {}

    public function registerNewUser(array $data): User
    {
        Db::beginTransaction();
        try {
            $user = $this->userRepository->createUser($data);

            $walletCreated = $this->walletServiceClient->createWalletForUser($user->id);

            if (!$walletCreated) {
                throw new BusinessException(message: 'Falha no processo de criação da carteira do usuário.');
            }

            Db::commit();

            return $user;
        } catch (Throwable $e) {
            Db::rollBack();
            throw $e;
        }
    }
}