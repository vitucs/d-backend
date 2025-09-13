<?php

declare(strict_types=1);

namespace App\Service\Client;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Psr\Log\LoggerInterface;

class NotificationServiceClient
{
    private $client;
    private string $url;

    public function __construct(
        ClientFactory $clientFactory,
        ConfigInterface $config,
        private LoggerInterface $logger
    ) {
        $this->client = $clientFactory->create(['timeout' => 5.0]);
        $this->url = $config->get('services.external_notification_service_url');
    }

    public function send(string $email, string $message): bool
    {
        try {
            $response = $this->client->post($this->url, [
                'json' => [
                    'email' => $email,
                    'message' => $message,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if ($data['message'] !== 'Success') {
                 throw new \Exception('Serviço externo de notificação retornou falha.');
            }
            
            return true;

        } catch (\Throwable $e) {
            $this->logger->error('Falha ao enviar notificação', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}