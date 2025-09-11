<?php

declare(strict_types=1);

namespace App\DTO;

class TransactionData
{
    public function __construct(
        public readonly int $payerId,
        public readonly int $payeeId,
        public readonly float $amount
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            payerId: (int) $data['payer_id'],
            payeeId: (int) $data['payee_id'],
            amount: (float) $data['amount']
        );
    }

    public static function fromRequest(array $validated): self
    {
        return self::fromArray($validated);
    }
}
