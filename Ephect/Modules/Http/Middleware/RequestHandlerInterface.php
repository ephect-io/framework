<?php

namespace Ephect\Modules\Http\Middleware;

use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Http\Transport\Response;

interface RequestHandlerInterface
{
    public function handle(Request $request): Response;
}
