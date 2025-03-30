<?php

namespace Ephect\Modules\DataAccess\Middlewares;

use Ephect\Framework\Middlewares\ApplicationStateMiddlewareInterface;
use Ephect\Modules\DataAccess\Client\PDO\PdoConnection;
use function Ephect\Hooks\useState;

class ConnectionOpenerMiddleware implements ApplicationStateMiddlewareInterface
{

    public function __invoke(object $arguments)
    {
        $conn = PdoConnection::opener($arguments->conf);
        useState(["$arguments->conf" => $conn,]);
        // Dispatch event
    }
}