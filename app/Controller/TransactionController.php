<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\TransactionException;
use App\Service\TransactionService;
use App\Request\Transaction\TransferRequest;
use Hyperf\Validation\ValidationException;
use App\DTO\TransactionData;

class TransactionController extends AbstractController
{
    public function __construct(
        private TransactionService $transactionService,
    ) {}

    public function transfer(TransferRequest $request)
    {
        $data = $request->validated();
        try {
            $transactionData = TransactionData::fromRequest($data);
            $this->transactionService->handle($transactionData);
            return $this->responseService->success(message: 'Transação realizada com sucesso.');
        } catch (ValidationException $e) {
            return $this->responseService->validationError($e->validator->errors()->all(),'Dados de entrada inválidos');  
        } catch (TransactionException $e) {
            return $this->responseService->error($e->getMessage(), $e->getCode());
        } catch (\Throwable $e) {
            return $this->responseService->serverError();
        }
    }
}