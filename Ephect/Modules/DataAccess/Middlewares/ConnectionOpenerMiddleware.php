<?php

namespace Ephect\Modules\DataAccess\Middlewares;

use Ephect\Framework\Logger\Logger;
use Ephect\Framework\Middlewares\ApplicationStateMiddlewareInterface;
use Ephect\Modules\DataAccess\Client\PDO\PdoConnection;
use function Ephect\Hooks\useMemory;
use function Ephect\Hooks\useState;

class ConnectionOpenerMiddleware implements ApplicationStateMiddlewareInterface
{

    public function __invoke(object $arguments): void
    {
        $conn = PdoConnection::opener($arguments->conf);
        useMemory(["$arguments->conf" => $conn,]);
        // Dispatch event
    }
}