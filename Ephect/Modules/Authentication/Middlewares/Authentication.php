<?php

namespace Ephect\Plugins\Authentication\Middlewares;

use Ephect\Modules\Http\Transport\RedirectResponse;
use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Http\Transport\Response;
use Ephect\Modules\Http\Middleware\MiddlewareInterface;
use Ephect\Modules\Http\Middleware\RequestHandlerInterface;
use Ephect\Modules\HttpStorage\Session\SessionInterface;
use Ephect\Modules\Authentication\Common\Configuration;
use Ephect\Modules\Notifier\Base\NotificationInterface;

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
