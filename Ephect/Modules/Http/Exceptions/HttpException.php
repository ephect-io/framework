<?php

namespace Ephect\Modules\Http\Exceptions;

use Ephect\Framework\Exceptions\BaseException;
use Ephect\Modules\Http\Transport\HttpStatusCodeEnum;
use Throwable;

class HttpException extends BaseException
{
    public function __construct(
        string                 $message = "",
        int|HttpStatusCodeEnum $code = 0,
        null|Throwable         $previous = null,
        mixed                  ...$params
    )
    {
        $status = $code;
        if ($status instanceof HttpStatusCodeEnum) {
            $status = $code->value;
        }

        $baseMessage = sprintf($message, $params);
        parent::__construct(
            'An HTTP exception occurred with the message:%s %s',
            $status,
            $previous,
            PHP_EOL, $baseMessage
        );
    }
}
