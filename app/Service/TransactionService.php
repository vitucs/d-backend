<?php

namespace App\Service;

use App\Exception\TransactionException;
use App\Job\NotificationJob;
use App\Model\User;
use App\Repository\UserRepository;
use App\Repository\WalletRepository;
use App\Service\Client\AuthorizationServiceClient;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\DbConnection\Db;
use Throwable;

class TransactionService
{
    public function __construct(
        private UserRepository $userRepository,
        private WalletRepository $walletRepository,
        private AuthorizationServiceClient $authClient,
        private DriverFactory $driverFactory
    ) {}

    public function handle(int $payerId, int $payeeId, float $amount): void
    {
        $payer = $this->userRepository->findById($payerId);
        $payee = $this->userRepository->findById($payeeId);

        $this->validateTransaction($payer, $amount);

        if (!$this->authClient->isAuthorized()) {
            throw new TransactionException('Transação não autorizada.', 403);
        }

        $this->executeTransaction($payer, $payee, $amount);
        
        $this->dispatchNotification($payee, $amount);
    }

    private function validateTransaction(User $payer, float $amount): void
    {
        if ($payer->type === 'shopkeeper') {
            throw new TransactionException('Lojistas não podem realizar transferências, apenas receber.', 403);
        }

        if ($payer->wallet->balance < $amount) {
            throw new TransactionException('Saldo insuficiente.', 400);
        }
    }
    
    private function executeTransaction(User $payer, User $payee, float $amount): void
    {
        try {
            Db::beginTransaction();

            $payerDecremented = $this->walletRepository->decrementBalance($payer->wallet, $amount);
            if (!$payerDecremented) {
                throw new TransactionException('Saldo insuficiente ou erro ao debitar.', 500);
            }
            
            $this->walletRepository->incrementBalance($payee->wallet, $amount);

            Db::commit();
        } catch (Throwable $e) {
            Db::rollBack();
            throw new TransactionException('Ocorreu um erro ao processar a transação.', 500, $e);
        }
    }
    
    private function dispatchNotification(User $payee, float $amount): void
    {
        $message = "Você recebeu uma transferência de R$ " . number_format($amount, 2, ',', '.');
        $job = new NotificationJob($payee->email, $message);
        
        // Pega o driver 'default' da fila e envia o job
        $queue = $this->driverFactory->get('default');
        $queue->push($job);
    }
}