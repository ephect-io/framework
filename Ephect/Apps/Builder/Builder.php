<?php

namespace Ephect\Apps\Builder;

use Ephect\Apps\Builder\Copiers\TemplatesCopyMaker;
use Ephect\Apps\Builder\Copiers\TemplatesCopier;
use Ephect\Apps\Builder\Descriptors\ComponentListDescriptor;
use Ephect\Apps\Builder\Descriptors\ModuleListDescriptor;
use Ephect\Apps\Builder\Descriptors\PluginListDescriptor;
use Ephect\Apps\Builder\Routes\Finder;
use Ephect\Apps\Builder\Strategy\BuildByNameStrategy;
use Ephect\Apps\Builder\Strategy\BuildByRouteStrategy;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Utils\File;
use Ephect\Plugins\Router\RouterService;

class Builder
{

    protected array $list = [];
    protected array $routes = [];

    public static function purgeCopies(): void
    {
        File::delTree(COPY_DIR);
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

            $copier = new TemplatesCopyMaker;

            $copier->makeCopies(true); // make unique copies

            CodeRegistry::load();

            $descriptor = new ComponentListDescriptor;
            $components = $descriptor->describe();
            $this->list = [...$this->list, ...$components];

            [$filename, $modulePaths]  = PluginRegistry::readPluginPaths();
            foreach ($modulePaths as $path) {
                $moduleConfigDir = $path . DIRECTORY_SEPARATOR . REL_CONFIG_DIR;
                $moduleSrcPathFile = $moduleConfigDir . REL_CONFIG_APP;
                $moduleSrcPath = file_exists($moduleSrcPathFile) ? $path . DIRECTORY_SEPARATOR . file_get_contents($moduleSrcPathFile) : $path . DIRECTORY_SEPARATOR . REL_CONFIG_APP;

                if (!ComponentRegistry::load()) {
                    $descriptor = new ModuleListDescriptor($path);
                    $moduleComponents = $descriptor->describe($moduleSrcPath);
                    $this->list = [...$this->list, ...$moduleComponents];
                }
            }

            CodeRegistry::save();
            ComponentRegistry::save();
        }

        if (!PluginRegistry::load()) {
            $descriptor = new PluginListDescriptor;
            $plugins = $descriptor->describe();
            $this->list = [...$this->list, ...$plugins];

            PluginRegistry::save();
            ComponentRegistry::save();
       }
    }

    public function prepareRoutedComponents(): void
    {
        CodeRegistry::load();
        ComponentRegistry::load();

        $routes =  (new Finder)->searchForRoutes();

        array_unshift($routes, 'App');

        foreach ($routes as $route) {
            $fqRoute = ComponentRegistry::read($route);
            $comp = $this->list[$fqRoute];

            $comp->copyComponents($this->list);
        }

        $this->routes = $routes;
    }

    /**
     * @throws \Exception
     */
    public function buildAllRoutes(): void
    {

        (new BuildByNameStrategy)->build('App');
        $this->routes = RouterService::findRouteNames();

        $buildByRoute = new BuildByRouteStrategy;
        foreach ($this->routes as $route) {
            $buildByRoute->build($route);
        }
    }
}
