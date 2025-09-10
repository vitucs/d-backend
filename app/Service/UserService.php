<?php
// app/Service/UserService.php

namespace App\Service;

use App\Exception\UserCreationException;
use App\Model\User;
use App\Model\Wallet;
use Hyperf\DbConnection\Db;
use Throwable;

class UserService
{
    public function createUser(array $data): User
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        Db::beginTransaction();
        try {
            $user = User::create($data);

            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00
            ]);

            Db::commit();

            return $user;
        } catch (Throwable $e) {
            Db::rollBack();
            throw new UserCreationException('Não foi possível criar o usuário.', 500, $e);
        }
    }
}