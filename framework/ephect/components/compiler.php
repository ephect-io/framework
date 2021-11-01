<?php

namespace Ephect\Components;

use Ephect\Cache\Cache;
use Ephect\Components\Generators\ComponentParser;
use Ephect\Components\Generators\ParserService;
use Ephect\IO\Utils as IOUtils;
use Ephect\Plugins\Route\RouteBuilder;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\PluginRegistry;
use Ephect\Tasks\Task;
use Ephect\Tasks\TaskRunner;
use Ephect\Tasks\TaskStructure;
use parallel\{channel};

class Compiler
{

    protected $list = [];
    protected $routes = [];

    /** @return void  */
    public function perform(): void
    {
        if (!ComponentRegistry::uncache()) {
            IOUtils::safeMkDir(CACHE_DIR);
            IOUtils::safeMkDir(COPY_DIR);
            IOUtils::safeMkDir(STATIC_DIR);

            CodeRegistry::uncache();

            $compList = [];
            $templateList = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);
            foreach ($templateList as $key => $compFile) {

                $cachedSourceViewFile = Component::getFlatFilename($compFile);
                copy(SRC_ROOT . $compFile, COPY_DIR . $cachedSourceViewFile);

                $comp = new Component();
                $comp->load($cachedSourceViewFile);
                $comp->analyse();

                $parser = new ComponentParser($comp);
                $struct = $parser->doDeclaration();
                $decl = $struct->toArray();

                CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
                ComponentRegistry::write($cachedSourceViewFile, $comp->getUID());
                ComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

                $entity = ComponentEntity::buildFromArray($struct->composition);
                $comp->add($entity);

                $this->list[$comp->getFullyQualifiedFunction()] = $comp;
            }

            CodeRegistry::cache();
            ComponentRegistry::cache();

        }

        if (!PluginRegistry::uncache()) {
            $pluginList = IOUtils::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
            foreach ($pluginList as $key => $pluginFile) {
                $plugin = new Plugin();
                $plugin->load($pluginFile);
                $plugin->analyse();

                PluginRegistry::write($pluginFile, $plugin->getUID());
                PluginRegistry::write($plugin->getUID(), $plugin->getFullyQualifiedFunction());

            }
            PluginRegistry::cache();
            ComponentRegistry::cache();
        }
    }

    public function postPerform(): void
    {

        $routes = $this->searchForRoutes();

        array_unshift($routes, 'App');

        foreach ($routes as $route) {
            $fqRoute = ComponentRegistry::read($route);
            $comp = $this->list[$fqRoute];

            $comp->copyComponents($this->list);

        }

        $this->routes = $routes;
    }

    public function followRoutes(): void
    {

        foreach ($this->routes as $route) {

            $struct = new TaskStructure(['name' => $route, 'arguments' => [$route]]);
            $task = new Task($struct);
            $task->setCallback(function (string $route, string $framework_root, Channel $channel) {

                include $framework_root . 'bootstrap.php';

                PluginRegistry::uncache();

                $comp = new Component($route);
                $filename = $comp->getFlattenSourceFilename();

                ob_start();
                $comp->render();
                $html = ob_get_clean();

                $channel->send(['name' => $route, 'filename' => $filename, 'html' => $html]);
            });

            $runner = new TaskRunner($task);
            $runner->run();
            
            $result = $runner->getResult();

            $runner->close();

            $filename = $result['filename'];
            $html = $result['html'];

            if ($route === 'App') {
                continue;
            }

            IOUtils::safeWrite(STATIC_DIR . $filename, $html);
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

        $list = $items[$class];
        $struct = new ComponentDeclarationStructure($list);
        $decl = new ComponentDeclaration($struct);

        $first = $decl->getComposition();

        return $first;
    }

    protected function findRouter(array $items, string $name): ?ComponentEntity
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

    public function purgeCopies(): void
    {
        IOUtils::delTree(COPY_DIR);
    }
}
