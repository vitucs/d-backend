<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class TransactionException extends BusinessException
{
    public function __construct(string $message = 'Erro na transação.', int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}
