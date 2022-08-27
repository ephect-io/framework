<?php

namespace Ephect\Framework\Components;

use DateTime;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\IO\Utils as IOUtils;
use Ephect\Plugins\Route\RouteBuilder;
use Ephect\Framework\Registry\CacheRegistry;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Tasks\Task;
use Ephect\Framework\Tasks\TaskRunner;
use Ephect\Framework\Tasks\TaskStructure;
use Ephect\Framework\Web\Curl;
use Ephect\Plugins\Router\RouterService;
use parallel\{channel};
use Throwable;

class Compiler
{

    protected $list = [];
    protected $routes = [];


    private function describeCustomComponents(string $sourceDir, string $filename): void
    {
        $cachedSourceViewFile = Component::getFlatFilename($filename);
        copy($sourceDir . $filename, COPY_DIR . $cachedSourceViewFile);

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

    private function describePlugins(string $sourceDir, string $filename): void
    {
        $plugin = new Plugin();
        $plugin->load($filename);
        $plugin->analyse();

        PluginRegistry::write($filename, $plugin->getUID());
        PluginRegistry::write($plugin->getUID(), $plugin->getFullyQualifiedFunction());
    }

    /**
     * Register all components of the application
     *
     * @return void
     */
    public function perform(): void
    {
        if (!ComponentRegistry::uncache()) {
            IOUtils::safeMkDir(CACHE_DIR);
            IOUtils::safeMkDir(COPY_DIR);
            IOUtils::safeMkDir(UNIQUE_DIR);
            IOUtils::safeMkDir(STATIC_DIR);

            CodeRegistry::uncache();

            $templateList = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);
            foreach ($templateList as $key => $compFile) {
                $this->describeCustomComponents(SRC_ROOT, $compFile);
            }

            CodeRegistry::cache();
            ComponentRegistry::cache();
        }

        if (!PluginRegistry::uncache()) {
            $pluginList = IOUtils::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
            foreach ($pluginList as $key => $pluginFile) {
                $this->describePlugins(PLUGINS_ROOT, $pluginFile);
            }
            PluginRegistry::cache();
            ComponentRegistry::cache();
        }
    }

    public function performAgain(): void
    {
        $this->list = [];

        ComponentRegistry::reset();
        PluginRegistry::reset();
        CodeRegistry::reset();
        CacheRegistry::reset();

        $templateList = IOUtils::walkTreeFiltered(UNIQUE_DIR, ['phtml']);
        foreach ($templateList as $key => $compFile) {
            $this->describeCustomComponents(UNIQUE_DIR, $compFile);
        }

        $pluginList = IOUtils::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
        foreach ($pluginList as $key => $pluginFile) {
            $this->describePlugins(PLUGINS_ROOT, $pluginFile);
        }

        CodeRegistry::cache();
        PluginRegistry::cache();
        ComponentRegistry::cache();
    }

    public function postPerform(): void
    {

        CodeRegistry::uncache();
        ComponentRegistry::uncache();

        $routes = $this->searchForRoutes();

        array_unshift($routes, 'App');

        foreach ($routes as $route) {
            $fqRoute = ComponentRegistry::read($route);
            $comp = $this->list[$fqRoute];

            $comp->copyComponents($this->list);
        }

        $this->routes = $routes;
    }


    public function followRoutesByTask(): void
    {

        foreach ($this->routes as $route) {

            $struct = new TaskStructure(['name' => $route, 'arguments' => [$route]]);
            $task = new Task($struct);
            $task->setCallback(function (string $route, string $framework_root, Channel $channel) {

                include $framework_root . 'bootstrap.php';

                PluginRegistry::uncache();

                Console::write("Compiling %s ... ", ConsoleColors::getColoredString($route, ConsoleColors::LIGHT_CYAN));
                Console::getLogger()->info("Compiling %s ...", $route);

                $comp = new Component($route);
                $filename = $comp->getFlattenSourceFilename();

                $html = '';
                $error = '';

                try {

                    $time_start = microtime(true);

                    ob_start();

                    $functionArgs = RouterService::findRouteArguments($route);

                    $comp->render($functionArgs);
                    $html = ob_get_clean();

                    $time_end = microtime(true);

                    $duration = $time_end - $time_start;

                    $utime = sprintf('%.3f', $duration);
                    $raw_time = DateTime::createFromFormat('u.u', $utime);
                    $duration = substr($raw_time->format('u'), 0, 3);

                    Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
                } catch (Throwable $ex) {
                    $error = Console::formatException($ex);
                }

                $channel->send(['name' => $route, 'filename' => $filename, 'html' => $html, 'error' => $error]);
            });

            $runner = new TaskRunner($task);
            $runner->run();

            $result = $runner->getResult();

            $runner->close();

            $filename = $result['filename'];
            $html = $result['html'];
            $error = $result['error'];

            if ($error !== '') {
                Console::writeLine("FATAL ERROR!%s %s", PHP_EOL, ConsoleColors::getColoredString($error, ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
                break;
            }

            if ($route === 'App') {
                continue;
            }

            IOUtils::safeWrite(STATIC_DIR . $filename, $html);
        }
    }

    public function compileApp(): void
    {
        PluginRegistry::uncache();

        Console::write("Compiling %s ... ", ConsoleColors::getColoredString('App', ConsoleColors::LIGHT_CYAN));
        Console::getLogger()->info("Compiling %s ... ", 'App');

        $comp = new Component('App');
        $filename = $comp->getFlattenSourceFilename();

        $html = '';
        $error = '';

        try {

            $time_start = microtime(true);

            $functionArgs = RouterService::findRouteArguments('App');

            ob_start();
            $comp->render($functionArgs);
            $html = ob_get_clean();

            $time_end = microtime(true);

            $duration = $time_end - $time_start;

            $utime = sprintf('%.3f', $duration);
            $raw_time = DateTime::createFromFormat('u.u', $utime);
            $duration = substr($raw_time->format('u'), 0, 3);

            Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
        } catch (Throwable $ex) {
            $error = Console::formatException($ex);
        }

        if ($error !== '') {
            Console::writeLine("FATAL ERROR!%s %s", PHP_EOL, ConsoleColors::getColoredString($error, ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
        }

        IOUtils::safeWrite(STATIC_DIR . $filename, $html);

    } 

    public function followRoutes(): void
    {

        if($this->routes[0] === 'App') {
            // Remove App from routes
            array_shift($this->routes);
        }

        $port = '80';

        foreach ($this->routes as $route) {

            $queryString = RouterService::findRouteQueryString($route);
            if($queryString === null) {
                continue;
            }

            $filename = "$route.html";
            $outputFilename = "$route.out";

            Console::write("Compiling %s, ", ConsoleColors::getColoredString($route, ConsoleColors::LIGHT_CYAN));
            Console::write("querying %s ... ", ConsoleColors::getColoredString("http://localhost:$port" . $queryString, ConsoleColors::LIGHT_GREEN));

            Console::getLogger()->info("Compiling %s ...", $route);


            $curl = new Curl();
            $time_start = microtime(true);

            ob_start();
            [$code, $header, $html] = $curl->request("http://localhost:$port" . $queryString);
            IOUtils::safeWrite(STATIC_DIR . $filename, $html);
            $output = ob_get_clean();
            IOUtils::safeWrite(LOG_PATH . $outputFilename, $output);

            $time_end = microtime(true);

            $duration = $time_end - $time_start;

            $utime = sprintf('%.3f', $duration);
            $raw_time = DateTime::createFromFormat('u.u', $utime);
            $duration = substr($raw_time->format('u'), 0, 3);

            Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));

            if ($route === 'App') {
                continue;
            }

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
            // array_push($result, $root->getName());
        }

        $result = array_unique($result);

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

                $router = $router;
                break;
            }

            $router = $this->findRouter($items, $name);
            if ($router !== null) {
                break;
            }
        }

        return $router;
    }

    public static function purgeCopies(): void
    {
        IOUtils::delTree(COPY_DIR);
    }
}
