<?php

namespace Ephect\WebApp\Builder\Routing;

use Ephect\Forms\Components\ComponentDeclaration;
use Ephect\Forms\Components\ComponentDeclarationStructure;
use Ephect\Forms\Components\ComponentEntity;
use Ephect\Forms\Registry\CodeRegistry;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Routing\RouteBuilder;

class Finder
{
    public function searchForRoutes(): array
    {
        $result = [];

        $items = CodeRegistry::items();

        $root = $this->findRouter($items, 'App');
        if ($root !== null) {
            $routes = $root->items();
            foreach ($routes as $route) {
                $props = (object)$route->props();
                $rb = new RouteBuilder($props);
                $re = $rb->build();

                $result[] = $re->getRedirect();
            }
        }

        if ($root === null) {
            $root = $this->findFirstComponent($items, 'App');
            // array_push($result, $root->getName());
        }

        return array_unique($result);
    }

    private function findRouter(array $items, string $name): ?ComponentEntity
    {
        $class = ComponentRegistry::read($name);
        $list = $items[$class];

        $struct = new ComponentDeclarationStructure($list);

        $composition = $struct->composition;

        $router = null;
        foreach ($composition as $child) {
            $name = $child['name'];
            if ($name == 'Router') {
                $router = ComponentEntity::buildFromArray($composition);

                break;
            }

            $router = $this->findRouter($items, $name);
            if ($router !== null) {
                break;
            }
        }

        return $router;
    }

    private function findFirstComponent(array $items, string $name): ?ComponentEntity
    {
        $class = ComponentRegistry::read($name);

        $list = $items[$class];
        $struct = new ComponentDeclarationStructure($list);
        $decl = new ComponentDeclaration($struct);

        return $decl->getComposition();
    }
}