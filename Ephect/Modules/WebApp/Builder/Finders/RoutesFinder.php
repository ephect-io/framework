<?php

namespace Ephect\Modules\WebApp\Builder\Finders;

use Ephect\Modules\Forms\Components\ComponentDeclaration;
use Ephect\Modules\Forms\Components\ComponentDeclarationInterface;
use Ephect\Modules\Forms\Components\ComponentDeclarationStructure;
use Ephect\Modules\Forms\Components\ComponentEntity;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Routing\Builder\RouteBuilder;
use Ephect\Modules\Routing\Registry\RouteRegistry;
use PhpParser\Builder\Declaration;

class RoutesFinder implements FinderInterface
{
    public function find(): array
    {
        $result = [];

        $items = CodeRegistry::items();
        if(\Constants::IS_WEB_APP) {
            $root = 'App';
        } else {
            $comp = $this->findFirstComponent($items, 'App');
            $root = $comp->getName();
        }

        $router = $this->findRouter($items, $root);
        if ($router !== null) {
            $routes = $router->items();
            foreach ($routes as $route) {
                if($route->getName() !== 'Route') continue;

                $props = (object)$route->props();
                $rb = new RouteBuilder($props);
                $re = $rb->build();

                $result[] = $re->getRedirect();
            }
        }

        if ($router === null) {
            $router = $this->findFirstComponent($items, 'App');
            array_push($result, $router->getName());
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
        $getRoutes = [];
        foreach ($composition as $child) {
            $name = $child['name'];
            if ($name == 'Router') {
                $router = ComponentEntity::buildFromArray($composition);
            // } elseif ($name == 'Route') {
            //     $arguments = (object)$child['arguments'];
            //     if(property_exists($arguments, 'method') && $arguments->method === 'GET') {
            //         $getRoutes[] = $child;
            //     }

            }

            // if ($router !== null && \Constants::IS_WEB_APP) {
            if ($router !== null) {
                $router = $this->findRouter($items, $name);

                break;
            }

        }
        RouteRegistry::write('routes', $getRoutes);

        RouteRegistry::save();

        return $router;
    }

    public function findRoutes(array $items, string $name): array
    {
        $class = ComponentRegistry::read($name);
        $list = $items[$class];

        $struct = new ComponentDeclarationStructure($list);

        $composition = $struct->composition;

        $getRoutes = [];
        foreach ($composition as $child) {
            $name = $child['name'];
            if ($name == 'Route') {
                $arguments = (object)$child['arguments'];
                if(property_exists($arguments, 'method') && $arguments->method === 'GET') {
                    $getRoutes[] = $child;
                }

            }
        }
        RouteRegistry::write('routes', $getRoutes);

        RouteRegistry::save();

        return $getRoutes;
    }

    private function findFirstComponent(array $items, string $name): ?ComponentEntity
    {
        $class = ComponentRegistry::read($name);

        $list = $items[$class];
        $struct = new ComponentDeclarationStructure($list);
        $decl = new ComponentDeclaration($struct);

        return $decl->getComposition();
    }

    private function findSecondComponent(array $items, string $name): ?ComponentDeclarationInterface
    {
        $class = ComponentRegistry::read($name);

        $list = $items[$class];
        $struct = new ComponentDeclarationStructure($list);
        $decl = new ComponentDeclaration($struct);

        return $decl;
    }
}
