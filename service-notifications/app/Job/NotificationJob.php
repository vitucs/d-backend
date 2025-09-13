<?php

declare(strict_types=1);

namespace App\Job;

use App\Service\Client\NotificationServiceClient;
use Hyperf\AsyncQueue\Job;
use Hyperf\Context\ApplicationContext;
use Psr\Log\LoggerInterface;

class NotificationJob extends Job
{
    public function __construct(
        public string $payeeEmail,
        public float $amount,
        public string $payerName
    ) {}

    public function handle(): void
    {
        $container = ApplicationContext::getContainer();
        $notificationService = $container->get(NotificationServiceClient::class);
        $logger = $container->get(LoggerInterface::class);

        $message = sprintf(
            'Você recebeu uma transferência de R$ %.2f de %s.',
            $this->amount,
            $this->payerName
        );

        $logger->info("Enviando notificação para {$this->payeeEmail}...");

        $notificationService->send($this->payeeEmail, $message);

        $logger->info("Notificação para {$this->payeeEmail} enviada com sucesso.");
    }
}