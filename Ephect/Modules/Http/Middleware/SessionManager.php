<?php

namespace Ephect\Modules\Http\Middleware;

use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Http\Transport\Response;
use Ephect\Modules\HttpStorage\Session\SessionInterface;
use Exception;

readonly class SessionManager implements MiddlewareInterface
{

    public function __construct(
        private SessionInterface $session
    )
    {
    }

    /**
     * @throws Exception
     */
    public function process(Request $request, RequestHandlerInterface $requestHandler): Response
    {
//         $sessionId = $request->getCookies(session_name());
//         if(!empty($sessionId)) {
//             $this->session->start($sessionId);
//         }

//        $this->session->start();
        $request->setSession($this->session);

        return $requestHandler->handle($request);
    }
}
