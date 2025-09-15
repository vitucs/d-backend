<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Throwable;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private ValidatorFactoryInterface $validatorFactory
    ) {}

    public function create(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'full_name' => 'required|string|max:255',
            'document'  => 'required|string|unique:users,document',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'type'      => 'required|in:common,shopkeeper',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }

        try {
            $user = $this->userService->registerNewUser($validator->validated());
            $user->makeHidden('password');
            return $user;
        } catch (Throwable $e) {
            return $response->json(['errors' => "Ocorreu um erro interno ao criar o usuário: $e"])->withStatus(500);
        }
    }

    public function show(RequestInterface $request, ResponseInterface $response)
    {
        $userId = $request->route('id');
        $user = $this->userService->findUserById((int) $userId);

        if (!$user) {
            return $response->json(['message' => 'Usuário não encontrado.'])->withStatus(404);
        }

        $user->makeHidden('password');
        return $response->json($user);
    }

    public function addBalance(RequestInterface $request, ResponseInterface $response, int $id)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'value' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }

        try {
            $user = $this->userService->addValueToWallet($validator->validated(), $id);
            $user->makeHidden('password');
            return $user;
        } catch (Throwable $e) {
            return $response->json(['errors' => "Ocorreu um erro interno ao criar o usuário: $e"])->withStatus(500);
        }
    }
}