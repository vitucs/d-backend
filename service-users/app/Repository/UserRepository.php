<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;

class UserRepository
{
    public function createUser(array $data): User
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return User::create($data);
    }
}