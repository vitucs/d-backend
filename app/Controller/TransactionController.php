<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\TransactionException;
use App\Service\TransactionService;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class TransactionController extends AbstractController
{
    public function __construct(
        private TransactionService $transactionService,
        private ValidatorFactoryInterface $validatorFactory
    ) {}

    public function transfer(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'payer_id' => 'required|integer|exists:users,id',
            'payee_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }
        
        $data = $validator->validated();

        try {
            $this->transactionService->handle(
                $data['payer_id'],
                $data['payee_id'],
                (float) $data['amount']
            );
            
            return $response->json(['message' => 'Transação realizada com sucesso.'])->withStatus(201);
        } catch (TransactionException $e) {
            return $response->json(['error' => $e->getMessage()])->withStatus($e->getCode());
        } catch (\Throwable $e) {
            // Logar o erro $e
            return $response->json(['error' => 'Erro interno no servidor.'])->withStatus(500);
        }
    }
}