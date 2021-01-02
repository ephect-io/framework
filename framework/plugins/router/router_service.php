<?php

namespace FunCom\Plugins\Router;

use FunCom\Registry\RouteRegistry;

class RouterService
{
    public function __construct()
    {
        RouteRegistry::uncache();
    }

    public function addRoute(string $rule, string $redirect): void
    {
        RouteRegistry::write($rule, $redirect);
    }

    public function saveRoutes(): bool
    {
        return RouteRegistry::cache();
    }
}