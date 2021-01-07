<?php

namespace FunCom\Plugins\Router;

use FunCom\Plugins\Route\RouteEntity;
use FunCom\Registry\RouteRegistry;

class RouterService
{

    public function __construct()
    {
        RouteRegistry::uncache();
    }

    public function addRoute(string $method, string $rule, string $redirect): void
    {
        $methodRegistry = RouteRegistry::read($method) ?: [];

        if (!array_key_exists($rule, $methodRegistry)) {
            $methodRegistry[$rule] = $redirect;
            RouteRegistry::write($method, $methodRegistry);
        }
    }

    public function saveRoutes(): bool
    {
        return RouteRegistry::cache();
    }

    public function matchRoute(RouteEntity $route): ?array
    {
        if($route->getMethod() !== REQUEST_METHOD) {
            return null;
        }

        $matches = \preg_replace('@' . $route->getRule() . '@', $route->getRedirect(), REQUEST_URI);

        if ($matches === REQUEST_URI) {
            return null;
        }

        $baseurl = parse_url($matches);
        $path = $baseurl['path'];

        $parameters = [];

        if (isset($baseurl['query'])) {
            parse_str($baseurl['query'], $parameters);
        }

        return [$path, $parameters];
    }
}
