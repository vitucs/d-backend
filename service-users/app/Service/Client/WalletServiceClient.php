<?php

declare(strict_types=1);

namespace App\Service\Client;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use GuzzleHttp\Exception\RequestException;
use App\Exception\ServiceException;

class WalletServiceClient
{
    private $client;

    public function __construct(
        ClientFactory $clientFactory,
        ConfigInterface $config
    ) {
        $baseUrl = $config->get('services.transactions_service_url');
        $this->client = $clientFactory->create(['base_uri' => $baseUrl]);
    }

    public function createWalletForUser(int $userId): bool
    {
        try {
            $response = $this->client->post('/wallets', [
                'json' => ['user_id' => $userId]
            ]);

            return $response->getStatusCode() === 201;
        } catch (RequestException $e) {
            throw new ServiceException(
                'Não foi possível criar a carteira para o novo usuário.',
                503
            );
        }
    }
}