<?php

namespace App\Job;

use App\Service\Client\NotificationServiceClient;
use Hyperf\AsyncQueue\Job;
use Hyperf\Context\ApplicationContext;

class NotificationJob extends Job
{
    public function __construct(public string $email, public string $message) {}

    public function handle()
    {
        $container = ApplicationContext::getContainer();
        $notificationService = $container->get(NotificationServiceClient::class);
        
        $notificationService->sendNotification($this->email, $this->message);
    }
}