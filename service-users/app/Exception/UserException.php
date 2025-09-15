<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class UserException extends BusinessException
{
    public function __construct(string $message = 'Erro no usuário.', int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}
