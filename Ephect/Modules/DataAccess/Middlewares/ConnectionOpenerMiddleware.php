<?php

namespace Ephect\Modules\DataAccess\Middlewares;

use Ephect\Framework\Middlewares\ApplicationStateMiddlewareInterface;
use Ephect\Modules\DataAccess\Client\PDO\PdoConnection;

use function Ephect\Hooks\useMemory;

class ConnectionOpenerMiddleware implements ApplicationStateMiddlewareInterface
{
    public function __invoke(object $arguments): void
    {
        $conn = PdoConnection::opener($arguments->conf);
        useMemory(["$arguments->conf" => $conn,]);
        // Dispatch event
    }
}
