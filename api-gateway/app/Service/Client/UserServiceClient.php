<?php

declare(strict_types=1);

namespace App\Service\Client;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use App\Exception\UserException;

class UserServiceClient
{
    private $client;

    public function __construct(
        ClientFactory $clientFactory,
        ConfigInterface $config
    ) {
        $baseUrl = $config->get('services.users_service_url');

        $this->client = $clientFactory->create([
            'base_uri' => $baseUrl,
            'timeout' => 5.0,
        ]);
    }

    public function createUser(array $data): ResponseInterface
    {
        try {
            return $this->client->post('/users', ['json' => $data]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $body = json_decode($response->getBody()->getContents(), true);

                $errorMessage = $body['error'] ?? 'Ocorreu um erro durante a operacao.';

                throw new UserException($errorMessage, $statusCode);
            }

            throw new UserException('Não foi possível se comunicar com o serviço de usuarios.', 503);
        }
    }

    public function addBalance(float $value, int $id): ResponseInterface
    {
        try {
            return $this->client->post("/users/$id/balance", ['json' => ['value' => $value]]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $body = json_decode($response->getBody()->getContents(), true);

                $errorMessage = $body['error'] ?? 'Ocorreu um erro durante a operacao.';

                throw new UserException($errorMessage, $statusCode);
            }

            throw new UserException('Não foi possível se comunicar com o serviço de usuarios.', 503);
        }
    }
}
