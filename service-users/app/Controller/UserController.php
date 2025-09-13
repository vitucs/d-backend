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
            return $response->json(['errors' => 'Ocorreu um erro interno ao criar o usuário.'])->withStatus(500);
        }
    }
}