<?php

declare(strict_types=1);

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Context\Context;
use Exception;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(protected HttpResponse $response) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('Authorization');

        if (empty($token) || ! str_starts_with($token, 'Bearer ')) {
            return $this->response->json(['error' => 'Token not provided'])->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $token);

        try {
            $jwtSecret = env('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

            $request = Context::set(ServerRequestInterface::class, $request->withAttribute('userId', $decoded->sub));

            return $handler->handle($request);
        } catch (Exception $e) {
            return $this->response->json(['error' => 'Invalid token'])->withStatus(401);
        }
    }
}