<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\TransactionException;
use App\Job\NotificationJob;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use App\Service\Client\AuthorizationServiceClient;
use App\Service\Client\UserServiceClient;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\DbConnection\Db;
use Throwable;

class TransactionService
{
    public function __construct(
        private UserServiceClient $userServiceClient,
        private AuthorizationServiceClient $authServiceClient,
        private WalletRepository $walletRepository,
        private TransactionRepository $transactionRepository,
        private DriverFactory $driverFactory
    ) {}

    public function handleTransfer(int $payerId, int $payeeId, float $amount): void
    {
        $payer = $this->userServiceClient->findUserById($payerId);
        $payee = $this->userServiceClient->findUserById($payeeId);

        if (!$payer || !$payee) {
            throw new TransactionException('Usuário pagador ou recebedor inválido.', 404);
        }

        $payerWallet = $this->walletRepository->findByUserId($payer->id);
        if ($payer->type === 'shopkeeper') {
            throw new TransactionException('Lojistas não podem realizar transferências.', 403);
        }
        if (!$payerWallet || $payerWallet->balance < $amount) {
            throw new TransactionException('Saldo insuficiente.', 400);
        }

        if (!$this->authServiceClient->isAuthorized()) {
            throw new TransactionException('Transferência não autorizada.', 403);
        }

        $this->executeDatabaseTransaction($payerWallet, $payee->id, $amount);

        $payerDetails = $this->userServiceClient->findUserById($payerId);
        $this->dispatchNotification($payee->email, $amount, $payerDetails->full_name ?? 'Um usuário');
    }

    private function executeDatabaseTransaction(object $payerWallet, int $payeeId, float $amount): void
    {
        $payeeWallet = $this->walletRepository->findByUserId($payeeId);
        Db::beginTransaction();
        try {
            if (!$this->walletRepository->decrementBalance($payerWallet, $amount)) {
                 throw new TransactionException('Falha ao debitar saldo do pagador.', 500);
            }
            $this->walletRepository->incrementBalance($payeeWallet, $amount);
            $this->transactionRepository->logTransaction($payerWallet->user_id, $payeeId, $amount);

            Db::commit();
        } catch (Throwable $e) {
            Db::rollBack();
            throw new TransactionException('Ocorreu um erro ao processar a transação.', 500, $e);
        }
    }
    
    private function dispatchNotification(string $email, float $amount, string $payerName): void
    {
        $job = new NotificationJob($email, $amount, $payerName);
        $queue = $this->driverFactory->get('default');
        $queue->push($job);
    }
}