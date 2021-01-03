<?php

namespace FunCom\Plugins\Router;

use FunCom\Plugins\Route\RouteEntity;
use FunCom\Registry\RouteRegistry;

class RouterService
{
    protected $methodRegistry;

    public function __construct()
    {
        RouteRegistry::uncache();
        $this->methodRegistry = [];
    }

    public function addRoute(string $method, string $rule, string $redirect): void
    {
        $this->methodRegistry = RouteRegistry::read($method) ?: $this->methodRegistry;

        if (!array_key_exists($rule, $this->methodRegistry)) {
            $this->methodRegistry[$rule] = $redirect;
            RouteRegistry::write($method, $this->methodRegistry);
        }
    }

    public function saveRoutes(): bool
    {
        return RouteRegistry::cache();
    }

    public function matchRoute(RouteEntity $route): ?array
    {
        if($route->getMethod() !== $_SERVER['REQUEST_METHOD']) {
            return null;
        }

        $url = $_SERVER['REQUEST_URI'];
        $key = $route->getRule();
        $value = $route->getRedirect();

        $matches = \preg_replace('@' . $key . '@', $value, $url);

        if ($matches === $url) {
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
