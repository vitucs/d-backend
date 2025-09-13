<?php

declare(strict_types=1);

namespace App\Service\Client;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Psr\Log\LoggerInterface;

class AuthorizationServiceClient
{
    private $client;
    private string $url;

    public function __construct(
        ClientFactory $clientFactory,
        ConfigInterface $config,
        private LoggerInterface $logger
    ) {
        $this->client = $clientFactory->create(['timeout' => 5.0]);
        
        $this->url = $config->get('services.authorization_service_url');
    }

    public function isAuthorized(): bool
    {
        try {
            $response = $this->client->get($this->url);

            $data = json_decode($response->getBody()->getContents(), true);
            return isset($data['data']['authorization']) && $data['data']['authorization'] === true;

        } catch (\Throwable $e) {
            $this->logger->error('Falha ao comunicar com o serviço de autorização', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}