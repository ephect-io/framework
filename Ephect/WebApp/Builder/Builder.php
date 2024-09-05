<?php

namespace Ephect\WebApp\Builder;

use Ephect\Forms\Registry\CodeRegistry;
use Ephect\Forms\Registry\ComponentRegistry;
use Ephect\Forms\Registry\PluginRegistry;
use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Routing\RouterService;
use Ephect\WebApp\Builder\Copiers\TemplatesCopyMaker;
use Ephect\WebApp\Builder\Descriptors\ComponentListDescriptor;
use Ephect\WebApp\Builder\Descriptors\ModuleListDescriptor;
use Ephect\WebApp\Builder\Routing\Finder;
use Ephect\WebApp\Builder\Strategy\BuildByNameStrategy;
use Ephect\WebApp\Builder\Strategy\BuildByRouteStrategy;
use Exception;

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

            CodeRegistry::save();
            ComponentRegistry::save();
        }

        if(!PluginRegistry::load()) {

            [$filename, $modulePaths] = ModuleInstaller::readModulePaths();
            foreach ($modulePaths as $path) {
                $moduleConfigDir = $path . DIRECTORY_SEPARATOR . REL_CONFIG_DIR;
                $moduleSrcPathFile = $moduleConfigDir . REL_CONFIG_APP;
                $moduleSrcPath = file_exists($moduleSrcPathFile) ? $path . DIRECTORY_SEPARATOR . file_get_contents($moduleSrcPathFile) : $path . DIRECTORY_SEPARATOR . REL_CONFIG_APP;

                $descriptor = new ModuleListDescriptor($path);
                $moduleComponents = $descriptor->describe($moduleSrcPath);
                $this->list = [...$this->list, ...$moduleComponents];
            }

            CodeRegistry::save();
            PluginRegistry::save();
            ComponentRegistry::save();
        }
    }

    public function prepareRoutedComponents(): void
    {
        CodeRegistry::load();
        ComponentRegistry::load();

        $routes = (new Finder)->searchForRoutes();

        array_unshift($routes, 'App');

        foreach ($routes as $route) {
            $fqRoute = ComponentRegistry::read($route);
            $comp = $this->list[$fqRoute];

            $comp->copyComponents($this->list);
        }

        $this->routes = $routes;
    }

    /**
     * @throws Exception
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
