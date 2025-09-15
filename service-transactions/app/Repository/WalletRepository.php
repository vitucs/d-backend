<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Wallet;
use Hyperf\DbConnection\Db;

class WalletRepository
{
    public function createWallet(int $userId): Wallet
    {
        return Wallet::create(['user_id' => $userId, 'balance' => 0.00]);
    }

    public function findByUserId(int $userId): ?Wallet
    {
        return Wallet::where('user_id', $userId)->first();
    }

    public function decrementBalance(Wallet $wallet, float $amount): bool
    {
        $updatedRows = Db::table('wallets')
            ->where('id', $wallet->id)
            ->where('balance', '>=', $amount)
            ->decrement('balance', $amount);

        return $updatedRows > 0;
    }

    public function incrementBalance(Wallet $wallet, float $amount): bool
    {
        $updatedRows = Db::table('wallets')
            ->where('id', $wallet->id)
            ->increment('balance', $amount);

        return $updatedRows > 0;
    }
}