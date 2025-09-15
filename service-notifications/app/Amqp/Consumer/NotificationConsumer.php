<?php

namespace App\Amqp\Consumer;

use App\Job\NotificationJob;
use Hyperf\Amqp\Consumer;
use Hyperf\Amqp\Annotation\Consumer as ConsumerAnnotation;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\Context\ApplicationContext;
use PhpAmqpLib\Message\AMQPMessage;

class NotificationConsumer extends Consumer
{
    public function consumeMessage($data, AMQPMessage $message): string
    {
        try {
            echo "📥 Recebido do RabbitMQ: " . json_encode($data) . "\n";
            
            // Enfileirar no Redis via AsyncQueue
            $container = ApplicationContext::getContainer();
            $driverFactory = $container->get(DriverFactory::class);
            $queue = $driverFactory->get('default');
            
            $job = new NotificationJob($data['payeeEmail'], $data['amount'], $data['payerName']);
            $queue->push($job);
            
            echo "✅ Enfileirado no Redis para processamento\n";
            
            return Consumer::ACK;
        } catch (\Exception $e) {
            echo "❌ Erro ao processar: " . $e->getMessage() . "\n";
            return Consumer::NACK;
        }
    }
}
