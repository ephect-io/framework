<?php

namespace Ephect\Modules\Authentication\Middlewares;

use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Http\Transport\Response;
use Ephect\Modules\Http\Middleware\MiddlewareInterface;
use Ephect\Modules\Http\Middleware\RequestHandlerInterface;
use Ephect\Modules\HttpStorage\Session\Session;
use Ephect\Modules\Authentication\Exceptions\CsrfTokenMismatchException;
use \Throwable;

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
        $formToken = $request->getPostParams(Session::CSRF_TOKEN);

        if (!hash_equals($sessionToken, $formToken)) {
            throw new CsrfTokenMismatchException();
        }

        return $requestHandler->handle($request);
    }
}
