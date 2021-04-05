<?php

namespace Ephect\Components;

use Ephect\Cache\Cache;
use Ephect\Components\Generators\BlocksParser;
use Ephect\Components\Generators\ComponentParser;
use Ephect\IO\Utils as IOUtils;
use Ephect\Plugins\Route\RouteBuilder;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\PluginRegistry;

class Compiler
{

    protected $list = [];

    /** @return void  */
    public function perform(): void
    {
        if (!ComponentRegistry::uncache()) {
            Cache::createCacheDir();

            $compList = [];
            $templateList = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);
            foreach ($templateList as $key => $compFile) {

                $cachedSourceViewFile = Component::getFlatFilename($compFile);
                copy(SRC_ROOT . $compFile, CACHE_DIR . $cachedSourceViewFile);

                $comp = new Component();
                $comp->load($cachedSourceViewFile);
                $comp->analyse();

                $parser = new ComponentParser($comp);
                $parser->doComponents();
                $list = $parser->getList();

                CodeRegistry::write($comp->getFullyQualifiedFunction(), $list);
                ComponentRegistry::write($cachedSourceViewFile, $comp->getUID());

                $comp->compose();

                $this->list[$comp->getFullyQualifiedFunction()] = $comp;
            }
            CodeRegistry::cache();
            ComponentRegistry::cache();

            $blocksViews = [];
            foreach ($this->list as $class => $comp) {
                $parser = new BlocksParser($comp);
                $filename = $parser->doBlocks();

                if ($filename !== null && file_exists(CACHE_DIR . $filename)) {
                    array_push($blocksViews, $filename);
                }
            }

            if (count($blocksViews) > 0) {
                foreach ($blocksViews as $compFile) {
                    $comp = new Component();
                    $comp->load($compFile);
                    $comp->analyse();

                    ComponentRegistry::write($compFile, $comp->getUID());
                }
            }
        }

        if (!PluginRegistry::uncache()) {
            $pluginList = IOUtils::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
            foreach ($pluginList as $key => $pluginFile) {
                $plugin = new Plugin();
                $plugin->load($pluginFile);
                $plugin->analyse();

                PluginRegistry::write($pluginFile, $plugin->getUID());
            }
            PluginRegistry::cache();
            ComponentRegistry::cache();
        }
    }

    public function postPerform(): void
    {

        $routes = $this->searchForRoutes();
        $compViews = [];

        array_unshift($routes, 'App');

        foreach ($routes as $route) {
            $fqRoute = ComponentRegistry::read($route);
            $comp = $this->list[$fqRoute];

            $comp->copyComponents($this->list);

        }
    }

    public function searchForRoutes(): array
    {
        $result = [];

        $items = CodeRegistry::items();

        $root = $this->findRouter($items, 'App');
        if ($root !== null) {
            $routes = $root->items();
            foreach ($routes as $route) {
                $props = (object) $route->props();
                $rb = new RouteBuilder($props);
                $re = $rb->build();

                array_push($result, $re->getRedirect());
            }
        }

        if ($root === null) {
            $root = $this->findFirstComponent($items, 'App');
            array_push($result, $root->getName());
        }

        return $result;
    }

    protected function findFirstComponent(array $items, string $name): ?ComponentEntity
    {
        $class = ComponentRegistry::read($name);

        $composition = $items[$class];

        $first = ComponentEntity::buildFromArray($composition);

        return $first;
    }

    protected function findRouter(array $items, string $name): ?ComponentEntity
    {
        $class = ComponentRegistry::read($name);

        $composition = $items[$class];

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
}
