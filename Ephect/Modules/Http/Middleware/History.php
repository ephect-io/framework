<?php

namespace Ephect\Modules\Http\Middleware;

use Ephect\Modules\Http\Transport\HistoryInterface;
use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Http\Transport\Response;

class History implements MiddlewareInterface
{

    public function __construct(private readonly HistoryInterface $history)
    {
    }

    public function process(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $info = $request->getInfo();
        $this->history->set($info);

        return $requestHandler->handle($request);
    }
}
