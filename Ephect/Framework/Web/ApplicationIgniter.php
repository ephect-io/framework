<?php

namespace Ephect\Framework\Web;

use Ephect\Framework\Middlewares\ApplicationStateMiddlewareInterface;
use function Ephect\Hooks\useState;

class ApplicationIgniter
{
    public function ignite(): void
    {
        [$middlewares] = useState(get: 'middlewares');

        if ($middlewares === null) {
            return;
        }

        foreach ($middlewares as $className => $arguments) {
//            $filename = FrameworkRegistry::read($className);
            if (class_exists($className) && class_implements($className, ApplicationStateMiddlewareInterface::class)) {
//                include_once $filename;
                $middleware = new $className();
                $middleware((object)$arguments);
            }
        }
    }
}