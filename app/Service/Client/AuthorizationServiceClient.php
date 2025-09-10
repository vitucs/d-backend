<?php

namespace App\Service\Client;

use Hyperf\Guzzle\ClientFactory;

class AuthorizationServiceClient
{
    private const AUTH_URL = 'https://util.devi.tools/api/v2/authorize';
    
    public function __construct(private ClientFactory $clientFactory) {}

    public function isAuthorized(): bool
    {
        $client = $this->clientFactory->create();
        
        try {
            $response = $client->get(self::AUTH_URL);
            $data = json_decode($response->getBody()->getContents(), true);
            
            return $data['status'] === 'success' && $data['data']['authorization'] === true;
        } catch (\Throwable $e) {
            // Logar o erro aqui
            return false;
        }
    }
}