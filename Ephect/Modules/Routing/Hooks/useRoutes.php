<?php

namespace Ephect\Modules\Routing\Hooks;

use Ephect\Modules\Routing\Services\RouterService;
use Ephect\Modules\Routing\Registry\RouteRegistry;

function useRoutes(string $group = 'User'): array
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

        if($stuff->group !== $group) {
            continue;
        }
        $result[$stuff->redirect] = [
            'url' => $stuff->translate,
            'caption' => $stuff->caption,
        ];
    }
     
    return $result;
}