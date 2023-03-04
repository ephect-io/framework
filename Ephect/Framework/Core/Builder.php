<?php

namespace Ephect\Framework\Core;

use DateTime;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Components\Component;
use Ephect\Framework\Components\ComponentDeclaration;
use Ephect\Framework\Components\ComponentDeclarationStructure;
use Ephect\Framework\Components\ComponentEntity;
use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\Components\Plugin;
use Ephect\Framework\Components\Webcomponent;
use Ephect\Framework\IO\Utils as IOUtils;
use Ephect\Plugins\Route\RouteBuilder;
use Ephect\Framework\Registry\CacheRegistry;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Registry\WebcomponentRegistry;
use Ephect\Framework\Web\Curl;
use Ephect\Plugins\Router\RouterService;
use Throwable;

class Builder
{

    protected $list = [];
    protected $routes = [];

    /**
     * Register all components of the application
     *
     * @return void
     */
    public function describeComponents(): void
    {
        if (!ComponentRegistry::uncache()) {
            IOUtils::safeMkDir(CACHE_DIR);
            IOUtils::safeMkDir(COPY_DIR);
            IOUtils::safeMkDir(UNIQUE_DIR);
            IOUtils::safeMkDir(STATIC_DIR);

            CodeRegistry::uncache();

            $templateList = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);
            foreach ($templateList as $key => $compFile) {
                $this->describeCustomComponent(SRC_ROOT, $compFile);
            }

            CodeRegistry::cache();
            ComponentRegistry::cache();
        }

        if (!PluginRegistry::uncache()) {
            $pluginList = IOUtils::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
            foreach ($pluginList as $key => $pluginFile) {
                $this->describePlugin(PLUGINS_ROOT, $pluginFile);
            }
            PluginRegistry::cache();
            ComponentRegistry::cache();
        }

        if (!WebcomponentRegistry::uncache()) {
            $webcomponentList = IOUtils::walkTreeFiltered(CUSTOM_WEBCOMPONENTS_ROOT, ['phtml']);
            foreach ($webcomponentList as $key => $webcomponentFile) {
                $this->describeWebcomponent(CUSTOM_WEBCOMPONENTS_ROOT, $webcomponentFile);
            }
            WebcomponentRegistry::cache();
            ComponentRegistry::cache();
        }
    }

    public function prepareRoutedComponents(): void
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

    private function describeCustomComponent(string $sourceDir, string $filename): void
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

    private function describePlugin(string $sourceDir, string $filename): void
    {
        $plugin = new Plugin();
        $plugin->load($filename);
        $plugin->analyse();

        PluginRegistry::write($filename, $plugin->getUID());
        PluginRegistry::write($plugin->getUID(), $plugin->getFullyQualifiedFunction());
    }

    private function describeWebcomponent(string $sourceDir, string $filename): void
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
        WebcomponentRegistry::write($cachedSourceViewFile, $comp->getUID());
        WebcomponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        $this->list[$comp->getFullyQualifiedFunction()] = $comp;
    }

    public function buildWebcomponents(string $motherUID): void
    {
        
        $templateList = IOUtils::walkTreeFiltered(SITE_ROOT . CONFIG_WEBCOMPONENTS, ['phtml']);
        foreach ($templateList as $key => $filename) {
            $uid = ComponentRegistry::read($filename);
            $comp = new Webcomponent($uid, $motherUID);
            $comp->load($filename);
            $comp->parse();
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
            $this->describeCustomComponent(UNIQUE_DIR, $compFile);
        }

        $pluginList = IOUtils::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
        foreach ($pluginList as $key => $pluginFile) {
            $this->describePlugin(PLUGINS_ROOT, $pluginFile);
        }

        CodeRegistry::cache();
        PluginRegistry::cache();
        ComponentRegistry::cache();
    }

    public function buildByName($name): void
    {
        PluginRegistry::uncache();

        Console::write("Compiling %s ... ", ConsoleColors::getColoredString($name, ConsoleColors::LIGHT_CYAN));
        Console::getLogger()->info("Compiling %s ... ", $name);

        $comp = new Component($name);
        $filename = $comp->getFlattenSourceFilename();

        $html = '';
        $error = '';

        try {

            $time_start = microtime(true);

            $functionArgs = $name === 'App' ? [] : RouterService::findRouteArguments($name);

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

    public function buildByRoute($route = 'Default'): void
    {

        $port = IOUtils::safeRead(CONFIG_DIR . 'dev_port') ?? '80';

        if ($route === 'App') {
            return;
        }

        $queryString = $route === 'Default' ? '/' : RouterService::findRouteQueryString($route);
        if ($queryString === null) {
            return;
        }

        $filename = "$route.html";
        $outputFilename = "$route.out";

        Console::write("Compiling %s, ", ConsoleColors::getColoredString($route, ConsoleColors::LIGHT_CYAN));
        Console::write("querying %s ... ", ConsoleColors::getColoredString(CONFIG_HOSTNAME . ":$port" . $queryString, ConsoleColors::LIGHT_GREEN));

        Console::getLogger()->info("Compiling %s ...", $route);


        $curl = new Curl();
        $time_start = microtime(true);

        ob_start();
        [$code, $header, $html] = $curl->request(CONFIG_HOSTNAME . ":$port" . $queryString);
        IOUtils::safeWrite(STATIC_DIR . $filename, $html);
        $output = ob_get_clean();
        IOUtils::safeWrite(LOG_PATH . $outputFilename, $output);

        $time_end = microtime(true);

        $duration = $time_end - $time_start;

        $utime = sprintf('%.3f', $duration);
        $raw_time = DateTime::createFromFormat('u.u', $utime);
        $duration = substr($raw_time->format('u'), 0, 3);

        Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
    }

    public function buildAllRoutes(): void
    {

        $this->buildByName('App');
        $this->routes = RouterService::findRouteNames();

        foreach ($this->routes as $route) {
            $this->buildByRoute($route);
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
