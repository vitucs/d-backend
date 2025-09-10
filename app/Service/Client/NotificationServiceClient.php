<?php

namespace App\Service\Client;

use Hyperf\Guzzle\ClientFactory;

class NotificationServiceClient
{
    private const NOTIFY_URL = 'https://util.devi.tools/api/v1/notify';

    public function __construct(private ClientFactory $clientFactory) {}

    public function sendNotification(string $email, string $message): bool
    {
        $client = $this->clientFactory->create();

        try {
            $response = $client->post(self::NOTIFY_URL, [
                'json' => [
                    'email' => $email,
                    'message' => $message
                ]
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);

            return $data['status'] === 'success' && $data['data']['message'] === 'sent';
        } catch (\Throwable $e) {
            return false;
        }
    }
}