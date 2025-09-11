<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UserCreationException;
use App\Service\UserService;
use App\Request\User\RegistrationRequest;
use Hyperf\Validation\ValidationException;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function store(RegistrationRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $this->userService->createUser($validatedData);

            return $this->responseService->created(message: 'Usuário criado com sucesso');
        } catch (ValidationException $e) {
            return $this->responseService->validationError($e->validator->errors()->all(),'Dados de entrada inválidos');  
        } catch (UserCreationException $e) {
            return $this->responseService->error($e->getMessage(), $e->getCode());
        } catch (\Throwable $e) {
            return $this->responseService->serverError();
        }
    }
}