<?php

namespace Ephect\Plugins\Route;

use Ephect\Components\Builders\AbstractBuilder;

class RouteBuilder extends AbstractBuilder
{

    public function __construct(object $props)
    {
        parent::__construct($props, RouteStructure::class);
    }

    public function build(): RouteInterface
    {
        $route = parent::buildEx(RouteEntity::class);
        $route = $this->translateRoute($route);

        return $route;
    }

    private function translateRoute(RouteInterface $route): RouteInterface
    {

        $rule = $route->getRule();

        $re = '/(\([\w]+\))/m';
        $subst = '([\\\\w\\\\-]+)';

        $normalized = preg_replace($re, $subst, $rule);

        if ($normalized === $rule) {
            return $route;
        }

        $re = '/(.*)(\/\(([\w]+)\))(\/([\w]+))?/m';
        $subst = '$1?$3=\\$1&verb=$5';

        $translated = preg_replace($re, $subst, $rule);

        $struct = new RouteStructure(['method' => $route->getMethod(), 'rule' => $normalized, 'redirect' => $route->getRedirect(), 'translation' => $translated]);

        $newRoute = new RouteEntity($struct);

        return $newRoute;
    }
}
