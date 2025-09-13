<?php

declare(strict_types=1);

namespace App\Service\Client;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use App\ValueObject\User;
use App\Exception\ServiceException;

class UserServiceClient
{
    private $client;

    public function __construct(ClientFactory $clientFactory, ConfigInterface $config)
    {
        $this->client = $clientFactory->create(['base_uri' => $config->get('services.users_service_url')]);
    }

    public function findUserById(int $id): ?User
    {
        try {
            $response = $this->client->get("/users/{$id}");
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                return new User($data);
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}