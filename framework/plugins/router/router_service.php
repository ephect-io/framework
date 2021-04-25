<?php

namespace Ephect\Plugins\Router;

use Ephect\Plugins\Route\RouteEntity;
use Ephect\Registry\Registry;
use Ephect\Registry\RouteRegistry;

class RouterService
{

    public function __construct()
    {
        RouteRegistry::uncache();
    }

    public function tryRouting(): bool
    {
        $result = file_exists(CACHE_DIR . 'routes.json');

        return $result;
    }

    public function doRouting(): ?array 
    {
        if(!IS_WEB_APP) {
            return null;
        }

        $json = file_get_contents(CACHE_DIR . 'routes.json');
        $routes = json_decode($json);
        $method = REQUEST_METHOD;
        $methodRoutes = !isset($routes->$method) ? null : $routes->$method;

        if(null === $methodRoutes) {
            return null;
        }

        $path = '';
        $parameters = [];

        foreach($methodRoutes as $rule => $redirect) {

            $matches = \preg_replace('@' . $rule . '@', $redirect, REQUEST_URI);

            if ($matches === REQUEST_URI) {
                continue;
            }

            $baseurl = parse_url($matches);
            $path = $baseurl['path'];
    
            $parameters = [];
    
            if (isset($baseurl['query'])) {
                parse_str($baseurl['query'], $parameters);
            }

        break;
        }

        return [$path, $parameters];
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

    public function shiftRegistry(): void
    {
        $routes = Registry::item('router')['node'];
        array_shift($routes);
        Registry::write('router', 'node', $routes);
        
        if(count($routes) === 0) {
            rename(RouteRegistry::getCacheFilename(), CACHE_DIR . 'routes.json');
        }

    }
}
