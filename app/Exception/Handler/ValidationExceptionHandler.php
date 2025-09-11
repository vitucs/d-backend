<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof ValidationException) {
            $data = json_encode([
                'success' => false,
                'message' => 'Dados de entrada inválidos',
                'errors' => $throwable->validator->errors()
            ], JSON_UNESCAPED_UNICODE);

            return $response
                ->withAddedHeader('content-type', 'application/json; charset=utf-8')
                ->withStatus(422)
                ->withBody(new SwooleStream($data));
        }

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
