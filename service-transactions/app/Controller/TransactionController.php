<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\TransactionException;
use App\Repository\WalletRepository;
use App\Service\TransactionService;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Throwable;

class TransactionController extends AbstractController
{
    public function __construct(
        private ValidatorFactoryInterface $validatorFactory
    ) {}
    
    public function createWallet(RequestInterface $request, ResponseInterface $response, WalletRepository $walletRepository)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'user_id' => 'required|integer|unique:wallets,user_id',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }
        
        $wallet = $walletRepository->createWallet($validator->validated()['user_id']);
        return $response->json($wallet)->withStatus(201);
    }

    public function createTransfer(RequestInterface $request, ResponseInterface $response, TransactionService $transactionService)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'payer' => 'required|integer',
            'payee' => 'required|integer|different:payer',
            'value'   => 'required|numeric|min:0.01',
        ]);
        
        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }

        try {
            $data = $validator->validated();
            $transactionService->handleTransfer((int)$data['payer'], (int)$data['payee'], (float)$data['value']);
            
            return $response->json(['message' => 'Transferência realizada com sucesso.'])->withStatus(201);
        } catch (TransactionException $e) {
            return $response->json(['error' => $e->getMessage()])->withStatus($e->getCode());
        } catch (Throwable $e) {
            return $response->json(['error' => "Erro interno no servidor: $e"])->withStatus(500);
        }
    }
}