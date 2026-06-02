<?php

namespace Ephect\Plugins\Authentication\Middlewares;

use Ephect\Framework\Http\HistoryInterface;
use Ephect\Framework\Http\RedirectResponse;
use Ephect\Framework\Http\Request;
use Ephect\Framework\Http\Response;
use Ephect\Framework\Middleware\MiddlewareInterface;
use Ephect\Framework\Middleware\RequestHandlerInterface;
use Ephect\Framework\Session\SessionInterface;
use Ephect\Plugins\Authentication\Configuration;

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
