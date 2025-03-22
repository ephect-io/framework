<?php

namespace Ephect\Modules\DataAccess\Client\PDO\Exceptions;

use Exception;
use Throwable;

class PdoConnectionException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}