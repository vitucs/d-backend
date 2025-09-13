<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Transaction;

class TransactionRepository
{
    public function logTransaction(int $payerId, int $payeeId, float $amount): Transaction
    {
        return Transaction::create([
            'payer_id' => $payerId,
            'payee_id' => $payeeId,
            'amount' => $amount,
        ]);
    }
}