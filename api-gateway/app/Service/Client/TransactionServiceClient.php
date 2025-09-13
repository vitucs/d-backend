<?php

declare(strict_types=1);

namespace App\Service\Client;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use App\Exception\ServiceException;

class TransactionServiceClient
{
    private $client;

    public function __construct(
        ClientFactory $clientFactory,
        ConfigInterface $config
    ) {
        $baseUrl = $config->get('services.transactions_service_url');

        $this->client = $clientFactory->create([
            'base_uri' => $baseUrl,
            'timeout' => 5.0,
        ]);
    }

    public function createTransfer(array $data): ResponseInterface
    {
        try {
            return $this->client->post('/transfers', ['json' => $data]);
        } catch (RequestException $e) {
            $message = 'Erro ao se comunicar com o serviço de transações.';
            $statusCode = 503;

            if ($e->hasResponse()) {
                $message = $e->getResponse()->getBody()->getContents();
                $statusCode = $e->getResponse()->getStatusCode();
            }

            throw new ServiceException($message, $statusCode);
        }
    }
}