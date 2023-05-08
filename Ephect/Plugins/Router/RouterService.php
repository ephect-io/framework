<?php

namespace Ephect\Plugins\Router;

use Ephect\Framework\Components\Component;
use Ephect\Framework\IO\Utils;
use Ephect\Plugins\Route\RouteEntity;
use Ephect\Plugins\Route\RouteInterface;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\HttpErrorRegistry;
use Ephect\Framework\Registry\RouteRegistry;

use function Ephect\Hooks\useState;

class RouterService implements RouterServiceInterface
{

    public function __construct()
    {
        RouteRegistry::uncache();
        HttpErrorRegistry::uncache();
    }

    public static function findRouteArguments(string $route): ?array
    {
        $result = null;

        $routes = RouteRegistry::items();
        if (count($routes) === 0) {
            $routes = RouteRegistry::getCachedRoutes();
        }

        $routeParts = explode('\\', $route);
        if (count($routeParts) > 0) {
            $route = array_pop($routeParts);
        }

        $allroutes = [];

        foreach ($routes as $method => $rules) {
            $allroutes = array_merge($allroutes, $rules);
        }

        $allroutes = array_filter($allroutes, function ($item) use ($route) {
            return $item['redirect'] == $route && $item['translate'] != '';
        });

        if (count($allroutes) === 0) {
            return $result;
        }

        sort($allroutes);

        $query = parse_url('https://localhost' . $allroutes[0]['translate'], PHP_URL_QUERY);

        if ($query === null || $query === false) {
            return $result;
        }

        parse_str($query, $result);

        return $result;
    }

    public static function findRouteNames(): ?array
    {
        $result = null;

        $routes = RouteRegistry::items();
        if (count($routes) === 0) {
            $routes = RouteRegistry::getCachedRoutes();
        }

        $allroutes = $routes['GET'];

        $result = array_map(function ($item) {
            return $item['redirect'];
        }, $allroutes);

        $result = array_unique($result);

        return $result;
    }

    public static function findRouteByQueryString(string $query): ?string
    {
        $result = null;

        $routes = RouteRegistry::items();
        if (count($routes) === 0) {
            $routes = RouteRegistry::getCachedRoutes();
        }

        $allroutes = $routes['GET'];

        $allroutes = array_filter($allroutes, function ($item) use ($query) {
            return $item['translate'] == $query;
        });

        if (count($allroutes) === 0) {
            return $result;
        }

        sort($allroutes);

        $finalRoute = (object)$allroutes[0];

        $result = $finalRoute->redirect;

        return $result;
    }

    public static function findRouteQueryString(string $route): ?string
    {
        $result = null;

        $routes = RouteRegistry::items();
        if (count($routes) === 0) {
            $routes = RouteRegistry::getCachedRoutes();
        }

        $routeParts = explode('\\', $route);
        if (count($routeParts) > 0) {
            $route = array_pop($routeParts);
        }

        $allroutes = $routes['GET'];

        $allroutes = array_filter($allroutes, function ($item) use ($route) {
            return $item['redirect'] == $route && $item['translate'] != '';
        });

        if (count($allroutes) === 0) {
            return $result;
        }

        sort($allroutes);

        $finalRoute = (object)$allroutes[0];
        if ($finalRoute->rule === $finalRoute->normal) {
            $queryString = str_replace("\\", "", $finalRoute->translate);

            return $queryString;
        }

        $queryString = parse_url('http://localhost' . $finalRoute->translate, PHP_URL_QUERY);
        parse_str($queryString, $arguments);

        $queryString = $finalRoute->normal;
        foreach ($arguments as $argument => $value) {
            $queryString = str_replace('(' . $argument . ')', $value, $queryString);
            $queryString = str_replace('$', '', $queryString);
        }

        return $queryString;
    }

    public function findRoute(string &$html): void
    {
        $html = '';
        [$state, $setState] = useState();

        if (!isset($state->routes)) {
            return;
        }

        $responseCode = 404;
        $query = [];

        $c = count($state->routes);

        for ($i = 0; $i < $c; $i++) {
            $route = $state->routes[$i];
            $path = $route->path;
            $query = $route->query;
            $error = $route->error;
            $responseCode = $route->code;

            if ($responseCode === 200) {
                $i = $c;
            }
        }

        $this->renderRoute($responseCode === 200, $path, $query, $error, $responseCode, $html);
    }

    public function renderRoute(bool $pageFound, string $path, array $query, int $error, int $responseCode, string &$html): void
    {
        if (!$pageFound) {
            http_response_code($responseCode);
            $path = HttpErrorRegistry::read($responseCode);

            if (ComponentRegistry::read($path) === null) {
                $html = 'Page not found';
                $html = ($responseCode === 401) ? 'Bad request' : $html;
                return;
            }
        }

        $comp = new Component($path);
        $comp->render($query);
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
            $isExact = $stuff->exact;
            $error = $stuff->error;

            [$redirect, $parameters, $code] = $this->matchRouteEx($method, $rule, $redirect, $translation, $isExact);

            if ($code !== 200) {
                continue;
            }

            break;
        }

        return [$redirect, $parameters, $error, $code];
    }

    private function matchRouteEx(string $method, string $rule, string $redirect, string $translation, bool $isExact): ?array
    {
        if ($method !== REQUEST_METHOD) {
            return [$redirect, [], 401];
        }

        $prefix = '@';
        $suffix = '@su';

        if ($isExact) {
            $prefix = $prefix . '^';
            $suffix = '$' . $suffix;
        }

        // $request_uri = \preg_replace('@' . $rule . '@', $redirect, REQUEST_URI);
        preg_match($prefix . $rule . $suffix, REQUEST_URI, $matches);
        $request_uri = !isset($matches[0]) ? '' : $matches[0][0];


        if ($request_uri === '') {
            return [$redirect, [], 404];
        }

        if ($translation !== '') {
            $request_uri = preg_replace($prefix . $rule . $suffix, $translation, REQUEST_URI);
        }

        $baseurl = parse_url(SERVER_HOST . $request_uri);

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
            $methodRegistry[$route->getRule()] = [
                'rule' => $route->getRule(),
                'redirect' => $route->getRedirect(),
                'normal' => $route->getNormalized(),
                'translate' => $route->getTranslation(),
                'error' => $route->getError(),
                'exact' => $route->isExact(),
            ];
            RouteRegistry::write($route->getMethod(), $methodRegistry);
        }

        if (($error = $route->getError()) !== 0) {
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
            $route->getTranslation(),
            $route->isExact()
        );
    }
}
