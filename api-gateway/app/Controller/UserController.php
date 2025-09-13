<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Client\UserServiceClient;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

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
            'document' => 'required|string|max:14|unique:users,document',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'type' => 'required|in:common,shopkeeper',
        ]);

        if ($validator->fails()) {
            return $response->json(['errors' => $validator->errors()])->withStatus(422);
        }

        $data = $validator->validated();

        $serviceResponse = $this->userService->createUser($data);

        return $serviceResponse;
    }
}
