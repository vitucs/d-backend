<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\TransactionException;
use App\Service\Client\TransactionServiceClient;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Throwable;

class TransactionController extends AbstractController
{
    public function __construct(
        private TransactionServiceClient $transactionService,
        private ValidatorFactoryInterface $validatorFactory
    ) {}

    public function create(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'payer' => 'required|integer|gt:0',
            'payee' => 'required|integer|gt:0',
            'value'   => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }
        
        $data = $validator->validated();
        
        try {
            $serviceResponse = $this->transactionService->createTransfer($data);
            return $response->json(json_decode($serviceResponse->getBody()->getContents(), true))->withStatus($serviceResponse->getStatusCode());
        } catch (TransactionException $e) {
            return $response->json(['error' => $e->getMessage()])->withStatus($e->getCode());
        } catch (Throwable $e) {   
            return $response->json(['error' => 'Ocorreu um erro inesperado no servidor.'])->withStatus(500);
        }
    }
}