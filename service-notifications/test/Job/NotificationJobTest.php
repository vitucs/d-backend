<?php

declare(strict_types=1);

namespace HyperfTest\Job;

use App\Job\NotificationJob;
use App\Service\Client\NotificationServiceClient;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Container;
use Hyperf\Testing\TestCase;
use Mockery;
use Psr\Log\LoggerInterface;

class NotificationJobTest extends TestCase
{
    public function testJobHandleSendsNotification()
    {
        $payeeEmail = 'test@receiver.com';
        $amount = 150.50;
        $payerName = 'John Doe';
        
        $expectedMessage = 'Você recebeu uma transferência de R$ 150.50 de John Doe.';

        $notificationServiceMock = Mockery::mock(NotificationServiceClient::class);
        $notificationServiceMock->shouldReceive('send')
            ->with($payeeEmail, $expectedMessage)
            ->once()
            ->andReturn(true);
            
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('info')->twice();

        $container = Mockery::mock(Container::class);
        $container->shouldReceive('get')->with(NotificationServiceClient::class)->andReturn($notificationServiceMock);
        $container->shouldReceive('get')->with(LoggerInterface::class)->andReturn($loggerMock);
        ApplicationContext::setContainer($container);

        $job = new NotificationJob($payeeEmail, $amount, $payerName);
        $job->handle();

        $this->assertTrue(true);
    }
}