<?php

namespace Ephect\Modules\Routing\Services;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Http\Transport\Request;
use Ephect\Modules\Routing\Base\RouteInterface;
use Ephect\Modules\Routing\Registry\HttpErrorRegistry;
use Ephect\Modules\Routing\Registry\RouteRegistry;
use Ephect\Modules\WebApp\Registry\PageRegistry;

use function Ephect\Hooks\useMemory;
use function Ephect\Hooks\useStore;

class RouterService implements RouterServiceInterface
{
    public function __construct()
    {
        RouteRegistry::load();
        HttpErrorRegistry::load();
    }

    public static function findRouteArguments(string $route): ?array
    {
        $result = [];

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
            return null;
        }

        sort($allroutes);

        $query = parse_url('https://localhost' . $allroutes[0]['translate'], PHP_URL_QUERY);

        if ($query === null || $query === false) {
            return null;
        }

        parse_str($query, $result);

        return $result;
    }

    public static function findRouteNames(): ?array
    {
        $routes = RouteRegistry::items();
        if (count($routes) === 0) {
            $routes = RouteRegistry::getCachedRoutes();
        }

        $allroutes = $routes['GET'];

        $result = array_map(function ($item) {
            return $item['redirect'];
        }, $allroutes);

        return array_unique($result);
    }

    public static function findRouteByQueryString(string $query): ?string
    {
        $routes = RouteRegistry::items();
        if (count($routes) === 0) {
            $routes = RouteRegistry::getCachedRoutes();
        }

        $allroutes = $routes['GET'];

        $allroutes = array_filter($allroutes, function ($item) use ($query) {
            return $item['translate'] == $query;
        });

        if (count($allroutes) === 0) {
            return null;
        }

        sort($allroutes);

        $finalRoute = (object)$allroutes[0];

        $result = $finalRoute->redirect;

        return $result;
    }

    public static function findRouteQueryString(string $route): ?string
    {
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
            return null;
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
        [$routes] = useMemory(get: 'routes');

        if (count($routes ?? []) === 0) {
            return;
        }

        $responseCode = 404;
        $query = [];

        $c = count($routes);

        for ($i = 0; $i < $c; $i++) {
            $route = $routes[$i];
            $path = $route->path;
            $query = $route->query;
            $error = $route->error;
            $responseCode = $route->code;
            $middlewares = $route->middlewares ?? [];

            if ($responseCode === 200) {
                $i = $c;
            }
        }

        $this->renderRoute($responseCode === 200, $path, $query, $error, $responseCode, $middlewares, $html);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderRoute(
        bool $pageFound,
        string $path,
        array|object|null $query,
        int $error,
        int $responseCode,
        array $middlewares,
        string &$html
    ): void {
        if (!$pageFound) {
            http_response_code($responseCode);
            $path = HttpErrorRegistry::read($responseCode);

            if (ComponentRegistry::read($path) === null) {
                $html = 'Page not found';
                $html = ($responseCode === 400) ? 'Bad request' : $html;
                $html = ($responseCode === 401) ? 'Unauthorized' : $html;
                return;
            }
        }

        $request = new Request();

        if (count($middlewares)) {
            foreach ($middlewares as $middleware) {
                call_user_func($middleware);
            }
        }

        $page = PageRegistry::read($path);
        $uid = PageRegistry::read($page);

        $comp = new Component($path, $uid);
        $comp->render($query, $request);
    }

    public function doRouting(): ?array
    {
        if (!\Constants::IS_WEB_APP) {
            return null;
        }

        $routes = require RouteRegistry::getMovedPhpFilename();
        $method = \Constants::REQUEST_METHOD;
        $methodRoutes = !isset($routes[$method]) ? null : $routes[$method];

        if (null === $methodRoutes) {
            return null;
        }

        $redirect = '';
        $parameters = [];

        foreach ($methodRoutes as $rule => $settings) {
            $stuff = (object)$settings;
            $redirect = $stuff->redirect;
            $translation = $stuff->translate;
            $isExact = $stuff->exact;
            $error = $stuff->error;
            $middlewares = $stuff->middlewares;

            [$redirect, $parameters, $code] = $this->matchRouteEx($method, $rule, $redirect, $translation, $isExact);

            if ($code !== 200) {
                continue;
            }

            break;
        }

        return [$redirect, $parameters, $error, $code, $middlewares];
    }

    private function matchRouteEx(
        string $method,
        string $rule,
        string $redirect,
        string $translation,
        bool $isExact
    ): ?array {
        if ($method !== \Constants::REQUEST_METHOD) {
            return [$redirect, [], 401];
        }

        $prefix = '@';
        $suffix = '@su';

        if ($isExact) {
            $prefix = $prefix . '^';
            $suffix = '$' . $suffix;
        }

        // \Constants::REQUEST_URI = \preg_replace('@' . $rule . '@', $redirect, \Constants::REQUEST_URI);
        preg_match($prefix . $rule . $suffix, \Constants::REQUEST_URI, $matches);
        $request_uri = !isset($matches[0]) ? '' : $matches[0][0];


        if ($request_uri === '') {
            return [$redirect, [], 404];
        }

        if ($translation !== '') {
            $request_uri = preg_replace($prefix . $rule . $suffix, $translation, \Constants::REQUEST_URI);
        }

        $baseurl = parse_url(\Constants::SERVER_HOST . $request_uri);

        $parameters = [];

        if (isset($baseurl['query'])) {
            parse_str($baseurl['query'], $parameters);
        }

        return [$redirect, $parameters, 200];
    }

    public function addRoute(RouteInterface $route): void
    {
        $methodRegistry = RouteRegistry::read($route->getMethod()) ?: [];
        if (array_key_exists($route->getRule(), $methodRegistry)) {
            $currentRoute = $methodRegistry[$route->getRule()];
            $middlewares = $currentRoute['middlewares'];

            if (!empty($middlewares)) {
                $methodRegistry[$route->getRule()] = [
                    'rule' => $route->getRule(),
                    'redirect' => $route->getRedirect(),
                    'normal' => $route->getNormalized(),
                    'translate' => $route->getTranslation(),
                    'error' => $route->getError(),
                    'exact' => $route->isExact(),
                    'middlewares' => $middlewares,
                ];
                RouteRegistry::write($route->getMethod(), $methodRegistry);
            }
        }

        if (!array_key_exists($route->getRule(), $methodRegistry)) {
            $methodRegistry[$route->getRule()] = [
                'rule' => $route->getRule(),
                'redirect' => $route->getRedirect(),
                'normal' => $route->getNormalized(),
                'translate' => $route->getTranslation(),
                'error' => $route->getError(),
                'exact' => $route->isExact(),
                'middlewares' => $route->getMiddlewares(),
            ];
            RouteRegistry::write($route->getMethod(), $methodRegistry);
        }


        if (($error = $route->getError()) !== 0) {
            HttpErrorRegistry::write($error, $route->getRedirect());
        }
    }

    public function saveRoutes(): bool
    {
        return RouteRegistry::save() && HttpErrorRegistry::save();
    }

    public function moveCache(): void
    {
        $json = File::safeRead(RouteRegistry::getCacheFilename());
        $phpRoutes = Text::jsonToPhpReturnedArray($json);
        File::safeWrite(RouteRegistry::getMovedPhpFilename(), $phpRoutes);
        rename(RouteRegistry::getCacheFilename(), RouteRegistry::getMovedFilename());
    }

    public function matchRoute(RouteInterface $route): ?array
    {
        return $this->matchRouteEx(
            $route->getMethod(),
            $route->getRule(),
            $route->getRedirect(),
            $route->getTranslation(),
            $route->isExact(),
        );
    }

    public function purgeCopies(): void
    {
        //        File::delTree(\Constants::COPY_DIR);
    }
}
