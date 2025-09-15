<?php

declare(strict_types=1);

namespace App\Service\Client;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use App\Exception\ServiceException;
use App\Exception\TransactionException;

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
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $body = json_decode($response->getBody()->getContents(), true);

                $errorMessage = $body['error'] ?? 'Ocorreu um erro durante a transação.';

                throw new TransactionException($errorMessage, $statusCode);
            }

            throw new TransactionException('Não foi possível se comunicar com o serviço de transações.', 503);
        }
    }
}