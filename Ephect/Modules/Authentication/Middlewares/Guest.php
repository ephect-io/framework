<?php

namespace Ephect\Modules\Authentication\Middlewares;

use Ephect\Modules\Http\Transport\HistoryInterface;
use Ephect\Modules\Http\Transport\RedirectResponse;
use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Http\Transport\Response;
use Ephect\Modules\Http\Middleware\MiddlewareInterface;
use Ephect\Modules\Http\Middleware\RequestHandlerInterface;
use Ephect\Modules\HttpStorage\Session\SessionInterface;
use Ephect\Modules\Authentication\Common\Configuration;

class Guest implements MiddlewareInterface
{
    private bool $isAuthenticated = true;

    public function __construct(
        private readonly SessionInterface $session,
        private readonly HistoryInterface $history,
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $this->session->start();
        $this->history->set($request->getInfo());
        $lastGetRequest = $this->history->getLastGetRequest();
        $pathname = $lastGetRequest->getPathInfo();

        if ($pathname == '/login' || $pathname == '/register') {
            $pathname = '/dashboard';
        }

        if ($this->session->has(Configuration::AUTH_KEY)) {
            return new RedirectResponse($pathname);
        }

        return $requestHandler->handle($request);
    }
}
