<?php

namespace Ephect\Framework\Web;

use Ephect\Framework\Middlewares\ApplicationStateMiddlewareInterface;
use Ephect\Framework\Registry\FrameworkRegistry;
use function Ephect\Hooks\useState;

class ApplicationIgniter
{
    public function ignite(): void
    {

        [$state] = useState();

        if ($state === null || !isset($state['middlewares'])) {
            return;
        }

        $middlewares = (object)$state['middlewares'];
        foreach ($middlewares as $className => $arguments) {
            $filename = FrameworkRegistry::read($className);
            if (is_file($filename) && is_subclass_of($className, ApplicationStateMiddlewareInterface::class)) {
                include_once $filename;
                $middleware = new $className();
                $middleware->ignite((object)$arguments);
            }
        }
    }
}