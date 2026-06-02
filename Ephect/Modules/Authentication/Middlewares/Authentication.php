<?php

namespace Ephect\Plugins\Authentication\Middlewares;

use Ephect\Framework\Http\RedirectResponse;
use Ephect\Framework\Http\Request;
use Ephect\Framework\Http\Response;
use Ephect\Framework\Middleware\MiddlewareInterface;
use Ephect\Framework\Middleware\RequestHandlerInterface;
use Ephect\Framework\Session\SessionInterface;
use Ephect\Modules\Authentication\Configuration;
use Ephect\Modules\Authentication\NotificationInterface;

class Authentication implements MiddlewareInterface
{
    private bool $isAuthenticated = true;

    public function __construct(
        private readonly SessionInterface $session,
        private readonly NotificationInterface $flashMessage,
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $this->session->start();
        if (!$this->session->has(Configuration::AUTH_KEY)) {
            $this->flashMessage->setError('Please sign in.');
            return new RedirectResponse("/login");
        }

        return $requestHandler->handle($request);
    }
}
