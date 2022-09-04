<?php

namespace Ephect\Plugins\Route;

use Ephect\Framework\Components\Builders\AbstractBuilder;

class RouteBuilder extends AbstractBuilder
{

    public function __construct(object $props)
    {
        parent::__construct($props, RouteStructure::class);
    }

    public function build(): RouteInterface
    {
        $route = parent::buildEx(RouteEntity::class);
        $route = $this->translateQueryStringRoute($route);
        $route = $this->translateNamedArgumentsRoute($route);

        return $route;
    }

    private function translateQueryStringRoute(RouteInterface $route): RouteInterface
    {

        $rule = $route->getRule();

        $re = '/[a-z]+=(\(\.\*\)|\(\.\+\)|\(\\\\w\+\))/m';
        $re = '/(\(\.\*\)|\(\.\+\)|\(\\\\w\+\))/m';

        preg_match_all($re, $rule, $matches, PREG_SET_ORDER, 0);

        $translated = $rule;

        $c = count($matches);
        for($i = 0; $i < $c; $i++) {
            $match = preg_quote($matches[$i][0]);
            $argn = $i+1;
            $translated = preg_replace('/' . $match . '/m', '\\$' . $argn, $translated, 1);
        }

        if ($translated === $rule && $translated !== '/') {

            $re = '/([^\w]*)(\w+)(.*)/m';
            $subst = '/$2';

            $translated = preg_replace($re, $subst, $rule);

            if ($translated === $rule) {
                return $route;
            }
        }

        $struct = new RouteStructure([
            'method' => $route->getMethod(), 
            'rule' => $rule,
            'normalized' => $rule,
            'redirect' => $route->getRedirect(), 
            'translation' => $translated,
            'error' => $route->getError(),
            'exact' => $route->isExact()
        ]);

        $newRoute = new RouteEntity($struct);

        return $newRoute;
    }

    private function translateNamedArgumentsRoute(RouteInterface $route): RouteInterface
    {

        $rule = $route->getRule();

        $re = '/(\([\w]+\))/m';
        $subst = '(\\\\S+)';

        $normalized = preg_replace($re, $subst, $rule);

        if ($normalized === $rule) {
            return $route;
        }

        $re = '/([^\(]+)?(\/\(([\w\-]+)\))/m';
        preg_match_all($re, $rule, $matches, PREG_SET_ORDER, 0);

        $translated = $rule;

        $c = count($matches);
        for($i = 0; $i < $c; $i++) {
            $argn = $i+1;
            $match = preg_quote($matches[$i][2]);
            $subst = ($i === 0 ? '?':'&') . $matches[$i][3] . '=\\$' . $argn;
            $translated = preg_replace('@' . $match . '@m', $subst, $translated, 1);
        }

        $struct = new RouteStructure([
            'method' => $route->getMethod(),
            'rule' => $normalized,
            'normalized' => $rule,
            'redirect' => $route->getRedirect(),
            'translation' => $translated,
            'error' => $route->getError(),
            'exact' => $route->isExact()
        ]);

        $newRoute = new RouteEntity($struct);

        return $newRoute;
    }

}
