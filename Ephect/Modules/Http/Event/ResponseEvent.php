<?php

namespace Ephect\Modules\Http\Event;

use Ephect\Framework\Event\Event;
use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Http\Transport\Response;

class ResponseEvent extends Event
{
    public function __construct(
        private readonly Request $request,
        private readonly Response $response,
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
