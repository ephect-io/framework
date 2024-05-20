<?php

namespace Ephect\Plugins\Route;

use Ephect\Framework\Components\ComponentEntityInterface;
use Ephect\Framework\Components\ComponentParserMiddlewareInterface;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\RouteRegistry;
use ReflectionFunction;

class RouteParserMiddleware implements ComponentParserMiddlewareInterface
{
    public function parse(ComponentEntityInterface|null $parent, string $motherUID, string $funcName, string $props): void
    {
        if($parent == null || $parent->getName() != 'Route') {
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
            $isMiddleware = $attr->getName() == \Ephect\Plugins\Route\Attributes\RouteMiddleware::class;
            if ($isMiddleware) {
                break;
            }
        }
        if(!count($attrs) || !$isMiddleware) {
            throw new \Exception("$funcName is not a route middleware");
        }
        RouteRegistry::uncache();
        $methodRegistry = RouteRegistry::read($route->getMethod()) ?: [];

        if(isset($methodRegistry[$route->getRule()])) {
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
        RouteRegistry::cache();
    }
}