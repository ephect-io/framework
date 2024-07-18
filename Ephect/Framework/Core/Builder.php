<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Components\Component;
use Ephect\Framework\Components\ComponentDeclaration;
use Ephect\Framework\Components\ComponentDeclarationStructure;
use Ephect\Framework\Components\ComponentEntity;
use Ephect\Framework\Components\Generators\ComponentParser;
use Ephect\Framework\Components\Generators\ParserService;
use Ephect\Framework\Components\Plugin;
use Ephect\Framework\Components\WebComponent;
use Ephect\Framework\Utils\File;
use Ephect\Plugins\Route\RouteBuilder;
use Ephect\Plugins\Router\RouterService;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;
use Ephect\Framework\Web\Curl;
use DateTime;
use Exception;
use Throwable;

class Builder
{

    protected array $list = [];
    protected array $routes = [];

    public static function purgeCopies(): void
    {
        File::delTree(COPY_DIR);
    }

    public function makeCopies(bool $unique = false)
    {
        if($unique) {
            File::safeMkDir(UNIQUE_DIR);
        } else {
            File::safeMkDir(COPY_DIR);
        }

        $bootstrapList = File::walkTreeFiltered(SRC_ROOT, ['phtml'], true);
        foreach ($bootstrapList as $key => $compFile) {
            if($unique) {
                $this->copyUniqueComponent(SRC_ROOT, $key, $compFile);
            } else {
                $this->copyComponent(SRC_ROOT, $key, $compFile);
            }
        }

        $pagesList = File::walkTreeFiltered(CUSTOM_PAGES_ROOT, ['phtml']);
        foreach ($pagesList as $key => $pageFile) {
            if($unique) {
                $this->copyUniqueComponent(CUSTOM_PAGES_ROOT, $key, $pageFile);
            } else {
                $this->copyComponent(CUSTOM_PAGES_ROOT, $key, $pageFile);
            }
        }

        $componentsList = File::walkTreeFiltered(CUSTOM_COMPONENTS_ROOT, ['phtml']);
        foreach ($componentsList as $key => $compFile) {
            if($unique) {
                $this->copyUniqueComponent(CUSTOM_COMPONENTS_ROOT, $key, $compFile);
            } else {
                $this->copyComponent(CUSTOM_COMPONENTS_ROOT, $key, $compFile);
            }
        }
    }

    public function copyComponent(string $path, string $key, string $filename): void
    {
        $root = pathinfo($path, PATHINFO_FILENAME) . DIRECTORY_SEPARATOR;

        if($root === APP_DIR) {
            $root = DIRECTORY_SEPARATOR;
        }

        $dirname = pathinfo($filename, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        $basename = pathinfo($filename, PATHINFO_BASENAME);
        File::safeMkDir(COPY_DIR . $root . $dirname);
        copy($path . $dirname . $basename, COPY_DIR . $root . $dirname . $basename);
    }

    public function copyUniqueComponent(string $path, string $key, string $filename): void
    {
        $root = pathinfo($path, PATHINFO_FILENAME) . DIRECTORY_SEPARATOR;

        if($root === APP_DIR) {
            $root = DIRECTORY_SEPARATOR;
        }

        $dirname = pathinfo($filename, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        $basename = pathinfo($filename, PATHINFO_BASENAME);
        File::safeMkDir(UNIQUE_DIR . $root . $dirname);
        copy($path . $dirname . $basename, UNIQUE_DIR . $root . $dirname . $basename);
    }

    /**
     * Register all components of the application
     *
     * @return void
     */
    public function describeComponents(): void
    {
        if (!ComponentRegistry::load()) {
            File::safeMkDir(CACHE_DIR);
            File::safeMkDir(COPY_DIR);
            File::safeMkDir(STATIC_DIR);

//            $this->makeCopies();
            $this->makeCopies(true);

            CodeRegistry::load();

            $bootstrapList = File::walkTreeFiltered(SRC_ROOT, ['phtml'], true);
            foreach ($bootstrapList as $key => $compFile) {
                $this->describeCustomComponent(SRC_ROOT, $compFile);
            }

            $pagesList = File::walkTreeFiltered(CUSTOM_PAGES_ROOT, ['phtml']);
            foreach ($pagesList as $key => $pageFile) {
                $this->describeCustomComponent(CUSTOM_PAGES_ROOT, $pageFile);
            }

            $componentsList = File::walkTreeFiltered(CUSTOM_COMPONENTS_ROOT, ['phtml']);
            foreach ($componentsList as $key => $compFile) {
                $this->describeCustomComponent(CUSTOM_COMPONENTS_ROOT, $compFile);
            }

            CodeRegistry::save();
            ComponentRegistry::save();
        }

        if (!PluginRegistry::load()) {
            $pluginList = File::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
            foreach ($pluginList as $key => $pluginFile) {
                $this->describePlugin(PLUGINS_ROOT, $pluginFile);
            }
            PluginRegistry::save();
            ComponentRegistry::save();
        }

        if (file_exists(CUSTOM_WEBCOMPONENTS_ROOT)) {
            if (!WebComponentRegistry::load()) {
                $webcomponentList = File::walkTreeFiltered(CUSTOM_WEBCOMPONENTS_ROOT, ['phtml']);
                foreach ($webcomponentList as $key => $webcomponentFile) {
                    $this->describeWebcomponent(CUSTOM_WEBCOMPONENTS_ROOT, $webcomponentFile);
                }
                CodeRegistry::save();
                WebComponentRegistry::save();
                ComponentRegistry::save();
            }
        }
    }

    private function describeCustomComponent(string $sourceDir, string $filename): void
    {
        File::safeMkDir(COPY_DIR . pathinfo($filename, PATHINFO_DIRNAME));
        copy($sourceDir . $filename, COPY_DIR . $filename);

        $comp = new Component();
        $comp->load($filename);

        $parser = new ParserService;
        $parser->doEmptyComponents($comp);
        if ($parser->getResult() === true) {
            $html = $parser->getHtml();
            File::safeWrite(COPY_DIR . $filename, $html);
            $comp->load($filename);
        }

        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        ComponentRegistry::write($filename, $uid);
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
        File::safeMkDir(COPY_DIR . pathinfo($filename, PATHINFO_DIRNAME));
        copy($sourceDir . $filename, COPY_DIR . $filename);

        $comp = new WebComponent();
        $comp->load($filename);
        $comp->analyse();

        $uid = $comp->getUID();
        $parser = new ComponentParser($comp);
        $struct = $parser->doDeclaration($uid);
        $decl = $struct->toArray();

        CodeRegistry::write($comp->getFullyQualifiedFunction(), $decl);
        WebComponentRegistry::write($filename, $uid);
        WebComponentRegistry::write($comp->getUID(), $comp->getFullyQualifiedFunction());

        $entity = ComponentEntity::buildFromArray($struct->composition);
        $comp->add($entity);

        $this->list[$comp->getFullyQualifiedFunction()] = $comp;
    }

    public function prepareRoutedComponents(): void
    {
        CodeRegistry::load();
        ComponentRegistry::load();

        $routes = $this->searchForRoutes();

        array_unshift($routes, 'App');

        foreach ($routes as $route) {
            $fqRoute = ComponentRegistry::read($route);
            $comp = $this->list[$fqRoute];

            $comp->copyComponents($this->list);
        }

        $this->routes = $routes;
    }

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

    protected function findFirstComponent(array $items, string $name): ?ComponentEntity
    {
        $class = ComponentRegistry::read($name);

        $list = $items[$class];
        $struct = new ComponentDeclarationStructure($list);
        $decl = new ComponentDeclaration($struct);

        return $decl->getComposition();
    }

    public function buildAllRoutes(): string
    {

        $motherUID = $this->buildByName('App');
        $this->routes = RouterService::findRouteNames();

        foreach ($this->routes as $route) {
            $this->buildByRoute($route);
        }

        return $motherUID;
    }

    public function buildByName(string $name): string
    {
        PluginRegistry::load();

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

        File::safeWrite(STATIC_DIR . $filename, $html);

        return $comp->getMotherUID();
    }

    /**
     * @throws Exception
     */
    public function buildByRoute($route = 'Default'): void
    {

        $port = trim(File::safeRead(CONFIG_DIR . 'dev_port') ?? '80');

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

        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8';
        $headers[] = 'Accept-Encoding: gzip, deflate, br';
        $headers[] = 'Connection: keep-alive';
        if (isset($_COOKIE['PHPSESSID'])) {
            $headers[] = "Cookie: PHPSESSID={$_COOKIE['PHPSESSID']};";
        }
        $headers[] = 'Upgrade-Insecure-Requests: 1';
        $headers[] = 'Sec-Fetch-Dest: document';
        $headers[] = 'Sec-Fetch-Mode: navigate';
        $headers[] = 'Sec-Fetch-Site: cross-site';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Cache-Control: no-cache';

        ob_start();
        [$code, $header, $html] = $curl->request(CONFIG_HOSTNAME . ":$port" . $queryString, $headers);
        File::safeWrite(STATIC_DIR . $filename, $html);
        $output = ob_get_clean();
        File::safeWrite(LOG_PATH . $outputFilename, $output);

        $time_end = microtime(true);

        $duration = $time_end - $time_start;

        $utime = sprintf('%.3f', $duration);
        $raw_time = DateTime::createFromFormat('u.u', $utime);
        $duration = substr($raw_time->format('u'), 0, 3);

        Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
    }
}
