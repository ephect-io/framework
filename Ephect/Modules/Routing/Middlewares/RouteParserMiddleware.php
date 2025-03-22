<?php

namespace Ephect\Modules\Routing\Middlewares;

use Ephect\Modules\Forms\Components\ComponentEntityInterface;
use Ephect\Modules\Forms\Middlewares\ComponentParserMiddlewareInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Routing\Attributes\RouteMiddleware;
use Ephect\Modules\Routing\Base\RouteStructure;
use Ephect\Modules\Routing\Entities\RouteEntity;
use Ephect\Modules\Routing\Registry\RouteRegistry;
use Exception;
use ReflectionFunction;

class RouteParserMiddleware implements ComponentParserMiddlewareInterface
{
    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props, array $arguments): void
    {
        if ($parent == null || $parent->getName() != 'Route') {
            return;
        }

        $filename = $motherUID . DIRECTORY_SEPARATOR . ComponentRegistry::read($funcName);

        $route = new RouteEntity(new RouteStructure($parent->props()));
        $middlewareHtml = "function() {\n\tinclude_once CACHE_DIR . '$filename';\n\t\$fn = \\{$funcName}($props); \$fn();\n}\n";
        include_once CACHE_DIR . $filename;
        $reflection = new ReflectionFunction($funcName);
        $attrs = $reflection->getAttributes();

        $isMiddleware = false;
        foreach ($attrs as $attr) {
            $isMiddleware = $attr->getName() == RouteMiddleware::class;
            if ($isMiddleware) {
                break;
            }
        }
        if (!count($attrs) || !$isMiddleware) {
            throw new Exception("$funcName is not a route middleware");
        }
        RouteRegistry::load();
        $methodRegistry = RouteRegistry::read($route->getMethod()) ?: [];

        if (isset($methodRegistry[$route->getRule()])) {
            $methodRegistry[$route->getRule()]['middlewares'][] = $middlewareHtml;
        } else {
            $methodRegistry[$route->getRule()] = [
                'rule' => $route->getRule(),
                'redirect' => $route->getRedirect(),
                'error' => $route->getError(),
                'exact' => $route->isExact(),
                'middlewares' => [$middlewareHtml,],
                'translate' => $route->getRule(),
                'normal' => $route->getRule(),
            ];
        }

        RouteRegistry::write($route->getMethod(), $methodRegistry);
        RouteRegistry::save();
    }
}