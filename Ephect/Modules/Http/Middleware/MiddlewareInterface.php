<?php

namespace Ephect\Modules\Http\Middleware;

use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Http\Transport\Response;

interface MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $requestHandler): Response;
}
