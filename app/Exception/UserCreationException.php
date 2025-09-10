<?php

namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class UserCreationException extends ServerException
{
    public function __construct(string $message, int $code = 400, Throwable $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}
