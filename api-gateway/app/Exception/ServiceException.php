<?php

declare(strict_types=1);

namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class ServiceException extends ServerException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
