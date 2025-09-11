<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class ResponseService
{
    public function __construct(
        private ResponseInterface $response
    ) {}

    public function success(mixed $data = null, string $message = 'Sucesso', int $status = 200): PsrResponseInterface
    {
        return $this->response->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ])->withStatus($status);
    }

    public function created(mixed $data = null, string $message = 'Criado com sucesso'): PsrResponseInterface
    {
        return $this->response->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ])->withStatus(201);
    }

    public function error(string $message = 'Erro', int $status = 400, mixed $errors = null): PsrResponseInterface
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $this->response->json($response)->withStatus($status);
    }

    public function validationError(array $errors, string $message = 'Dados de entrada inválidos'): PsrResponseInterface
    {
        return $this->response->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ])->withStatus(422);
    }

    public function notFound(string $message = 'Não encontrado'): PsrResponseInterface
    {
        return $this->response->json([
            'success' => false,
            'message' => $message
        ])->withStatus(404);
    }

    public function unauthorized(string $message = 'Não autorizado'): PsrResponseInterface
    {
        return $this->response->json([
            'success' => false,
            'message' => $message
        ])->withStatus(401);
    }

    public function forbidden(string $message = 'Acesso negado'): PsrResponseInterface
    {
        return $this->response->json([
            'success' => false,
            'message' => $message
        ])->withStatus(403);
    }

    public function serverError(string $message = 'Erro interno do servidor'): PsrResponseInterface
    {
        return $this->response->json([
            'success' => false,
            'message' => $message
        ])->withStatus(500);
    }
}
