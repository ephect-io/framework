<?php

namespace Ephect\Modules\Notifier\Middlewares;

use Ephect\Framework\Middlewares\ApplicationStateMiddlewareInterface;
use Exception;

readonly class NotificationMiddleware implements ApplicationStateMiddlewareInterface
{
    /**
     * @throws Exception
     */
    public function __invoke(object $arguments): void
    {
        // $request = $arguments->request;
        // $requestHandler = $arguments->requestHandler;
        // $notification = $arguments->notification;
        // $request->setNotification($notification);

        // $requestHandler->handle($request);
    }
}
