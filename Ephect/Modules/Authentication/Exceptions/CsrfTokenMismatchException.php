<?php

namespace Ephect\Modules\Authentication\Exceptions;

use Ephect\Modules\Http\Exceptions\HttpException;
use Ephect\Modules\Http\Transport\HttpStatusCodeEnum;
use Throwable;

class CsrfTokenMismatchException extends HttpException
{
    public function __construct(null | Throwable $previous = null)
    {
        parent::__construct(
            'Your request could not be validated. Please try again.',
            HttpStatusCodeEnum::FORBIDDEN_ACCESS,
            $previous,
        );
    }
}
