<?php
// app/Controller/UserController.php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UserCreationException;
use App\Service\UserService;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private ValidatorFactoryInterface $validatorFactory
    ) {}

    public function store(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validatorFactory->make($request->all(), [
            'full_name' => 'required|string|max:255',
            'document'  => 'required|string|unique:users,document',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'type'      => 'required|string|in:common,shopkeeper'
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }

        try {
            $user = $this->userService->createUser($validator->validated());
            return $response->json($user)->withStatus(201);
        } catch (UserCreationException $e) {
            return $response->json(['error' => $e->getMessage()])->withStatus($e->getCode());
        } catch (\Throwable $e) {
            return $response->json(['error' => 'Erro interno no servidor.'])->withStatus(500);
        }
    }
}