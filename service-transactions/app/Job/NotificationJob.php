<?php

declare(strict_types=1);

namespace App\Job;

use Hyperf\AsyncQueue\Job;

class NotificationJob extends Job
{
    public function __construct(
        public string $payeeEmail,
        public float $amount,
        public string $payerName
    ) {}

    public function handle(): void
    {
        // A lógica de verdade para este job estará no service-notifications.
        // Este arquivo é apenas o "contrato" da mensagem que será enviada.
    }
}