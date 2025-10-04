<?php

namespace Ephect\Modules\WebComponent\Middlewares;

use Ephect\Framework\Middlewares\ApplicationStateMiddlewareInterface;

class WebComponentBuilderMiddleware implements ApplicationStateMiddlewareInterface
{
    public function __invoke(object $arguments)
    {
        // TODO: Implement ignite() method.
        // add web component JS module to the list of scripts to load.
    }

}
