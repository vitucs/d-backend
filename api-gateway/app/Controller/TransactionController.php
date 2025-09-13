<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Client\TransactionServiceClient;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class TransactionController extends AbstractController
{
    public function __construct(
        private TransactionServiceClient $transactionService,
        private ValidatorFactoryInterface $validatorFactory
    ) {}

    public function create(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'payer_id' => 'required|integer|gt:0',
            'payee_id' => 'required|integer|gt:0',
            'amount'   => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }
        
        $data = $validator->validated();
        
        $serviceResponse = $this->transactionService->createTransfer($data);
        
        return $serviceResponse;
    }
}