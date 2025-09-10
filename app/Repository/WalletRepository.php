<?php

namespace App\Repository;

use App\Model\User;
use App\Model\Wallet;
use Hyperf\DbConnection\Db;

class WalletRepository
{
    public function incrementBalance(Wallet $wallet, float $amount): bool
    {
        return $wallet->update(['balance' => $wallet->balance + $amount]);
    }

    public function decrementBalance(Wallet $wallet, float $amount): bool
    {
        return Db::table('wallets')
            ->where('id', $wallet->id)
            ->where('balance', '>=', $amount)
            ->decrement('balance', $amount) > 0;
    }
}