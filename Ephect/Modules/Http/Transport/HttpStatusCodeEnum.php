<?php

namespace Ephect\Modules\Http\Transport;

enum HttpStatusCodeEnum: int
{
    case OK = 200;
    case TEMPORARY_REDIRECT = 301;
    case PERMANENT_REDIRECT = 302;
    case NOT_FOUND = 404;
    case BAD_REQUEST = 400;
    case NOT_AUTHORIZED = 401;
    case FORBIDDEN_ACCESS = 403;
    case METHOD_NOT_ALLOWED = 405;
    case SERVER_ERROR = 500;
}
