<?php

namespace Ephect\Plugins\Router;

use Ephect\Components\Component;
use Ephect\Plugins\Route\RouteEntity;
use Ephect\Plugins\Route\RouteInterface;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\HttpErrorRegistry;
use Ephect\Registry\RouteRegistry;

use function Ephect\Hooks\useState;

class RouterService
{

    public function __construct()
    {
        RouteRegistry::uncache();
        HttpErrorRegistry::uncache();
    }

    public function findRoute(string &$html): void
    {
        $html = '';
        [$state, $setState] = useState();

        if(!isset($state->routes)) {
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
        preg_match($prefix . $rule  . $suffix, REQUEST_URI, $matches);
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
                'redirect' => $route->getRedirect(),
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
