<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UserException;
use App\Service\Client\UserServiceClient;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Throwable;

class UserController extends AbstractController
{
    public function __construct(
        private UserServiceClient $userService,
        private ValidatorFactoryInterface $validatorFactory
    ) {}

    public function create(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'full_name' => 'required|string|max:255',
            'document' => 'required|string|max:14',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'type' => 'required|in:common,shopkeeper',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }

        $data = $validator->validated();

        try {
            $serviceResponse = $this->userService->createUser($data);
            return $response->json(json_decode($serviceResponse->getBody()->getContents(), true))->withStatus($serviceResponse->getStatusCode());
        } catch (UserException $e) {
            return $response->json(['error' => $e->getMessage()])->withStatus($e->getCode());
        } catch (Throwable $e) {   
            return $response->json(['error' => 'Ocorreu um erro inesperado no servidor.'])->withStatus(500);
        }
    }

    public function addBalance(RequestInterface $request, ResponseInterface $response, int $id)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'value' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }

        $data = $validator->validated();
        $valueToAdd = (float) $data['value'];
        
        try {
            $serviceResponse = $this->userService->addBalance($valueToAdd, $id);
            return $response->json(json_decode($serviceResponse->getBody()->getContents(), true))->withStatus($serviceResponse->getStatusCode());
        } catch (UserException $e) {
            return $response->json(['error' => $e->getMessage()])->withStatus($e->getCode());
        } catch (Throwable $e) {   
            return $response->json(['error' => 'Ocorreu um erro inesperado no servidor.'])->withStatus(500);
        }
    }
}
