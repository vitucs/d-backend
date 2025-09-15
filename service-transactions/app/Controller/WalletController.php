<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\TransactionException;
use App\Repository\WalletRepository;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Throwable;

class WalletController extends AbstractController
{
    public function __construct(
        private WalletRepository $walletRepository,
        private ValidatorFactoryInterface $validatorFactory
    ) {}

    public function addBalance(RequestInterface $request, ResponseInterface $response, int $id)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'value' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }

        try {
            $wallet = $this->walletRepository->findByUserId($id);

            if (!$wallet) {
                throw new TransactionException('Carteira não encontrada.', 404);
            }

            $this->walletRepository->incrementBalance($wallet, (float) $validator->validated()['value']);

            return $response->json(['message' => 'Saldo adicionado com sucesso.'])->withStatus(200);
        } catch (TransactionException $e) {
            return $response->json(['error' => $e->getMessage()])->withStatus($e->getCode());
        } catch (Throwable $e) {
            return $response->json(['error' => "Erro interno no servidor: $e"])->withStatus(500);
        }
    }
}
