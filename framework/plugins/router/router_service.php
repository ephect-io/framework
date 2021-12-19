<?php

namespace Ephect\Plugins\Router;

use Ephect\Plugins\Route\RouteEntity;
use Ephect\Plugins\Route\RouteInterface;
use Ephect\Registry\HttpErrorRegistry;
use Ephect\Registry\RouteRegistry;

class RouterService
{

    public function __construct()
    {
        RouteRegistry::uncache();
        HttpErrorRegistry::uncache();
    }

    public function routesAreCached(): bool
    {
        $result = file_exists(CACHE_DIR . 'routes.json');

        return $result;
    }

    public function doRouting(): ?array
    {
        if (!IS_WEB_APP) {
            return null;
        }

        $json = file_get_contents(CACHE_DIR . 'routes.json');
        $routes = json_decode($json);
        $method = REQUEST_METHOD;
        $methodRoutes = !isset($routes->$method) ? null : $routes->$method;

        if (null === $methodRoutes) {
            return null;
        }

        $redirect = '';
        $parameters = [];

        foreach ($methodRoutes as $rule => $stuff) {

            $redirect = $stuff->redirect;
            $translation = $stuff->translate;

            [$redirect, $parameters, $code] = $this->matchRouteEx($method, $rule, $redirect, $translation);

            if ($code !== 200) {
                continue;
            }

            break;
        }

        return [$redirect, $parameters, $code];
    }

    private function matchRouteEx(string $method, string $rule, string $redirect, string $translation): ?array
    {
        if ($method !== REQUEST_METHOD) {
            return ['Error401', [], 401];
        }

        $request_uri = \preg_replace('@' . $rule . '@', $redirect, REQUEST_URI);

        if ($request_uri === REQUEST_URI) {
            return ['Error404', [], 404];
        }

        if ($translation !== '') {
            $query = preg_replace('@' . $rule . '@', $translation, REQUEST_URI);

            $request_uri = SERVER_HOST . $query;
        }

        $baseurl = parse_url($request_uri);

        $parameters = [];

        if (isset($baseurl['query'])) {
            parse_str($baseurl['query'], $parameters);
        }

        return [$redirect, $parameters, 200];
    }

    public function addRoute(RouteInterface $route): void
    {
        $methodRegistry = RouteRegistry::read($route->getMethod()) ?: [];

        if (!array_key_exists($route->getRule(), $methodRegistry)) {
            $methodRegistry[$route->getRule()] = ['redirect' => $route->getRedirect(), 'translate' => $route->getTranslation(), 'error' => $route->getError()];
            RouteRegistry::write($route->getMethod(), $methodRegistry);
        }

        if(($error = $route->getError()) !== 0) {
            HttpErrorRegistry::write($error, $route->getRedirect());
        }
    }

    public function saveRoutes(): bool
    {
        return RouteRegistry::cache() && HttpErrorRegistry::cache();
    }

    public function matchRoute(RouteEntity $route): ?array
    {
        return $this->matchRouteEx(
            $route->getMethod(),
            $route->getRule(),
            $route->getRedirect(),
            $route->getTranslation()
        );
    }
}
