<?php

namespace Ephect\Plugins\Authentication\Middlewares;

use Ephect\Framework\Http\Request;
use Ephect\Framework\Http\Response;
use Ephect\Framework\Middleware\MiddlewareInterface;
use Ephect\Framework\Middleware\RequestHandlerInterface;
use Ephect\Framework\Session\Session;
use Ephect\Plugins\Authentication\Exceptions\CsrfTokenMismatchException;

class VerifyCsrfToken implements MiddlewareInterface
{

    /**
     * @throws CsrfTokenMismatchException
     */
    public function process(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        if ($request->getMethod() == 'GET') {
            return $requestHandler->handle($request);
        }

        $session = $request->getSession();

        $sessionToken = $session->read(Session::CSRF_TOKEN) ?? '';
        $formToken = $request->searchFromBody(Session::CSRF_TOKEN);

        if (!hash_equals($sessionToken, $formToken)) {
            throw new CsrfTokenMismatchException();
        }

        return $requestHandler->handle($request);
    }
}
