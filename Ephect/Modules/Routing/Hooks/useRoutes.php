<?php

namespace Ephect\Modules\Routing\Hooks;

use Ephect\Modules\Routing\Services\RouterService;
use Ephect\Modules\Routing\Registry\RouteRegistry;

function useRoutes(): array
{
    $result = [];

    $routes = RouteRegistry::getCachedRoutes();
    $method = \Constants::REQUEST_METHOD;
    $methodRoutes = !isset($routes[$method]) ? null : $routes[$method];

    if (null === $methodRoutes) {
        return $result;
    }

    foreach ($methodRoutes as $rule => $settings) {
        $stuff = (object)$settings;

        $result[$stuff->redirect] = $settings->translation;    
    }
     
    return $result;
}