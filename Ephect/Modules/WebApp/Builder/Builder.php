<?php

namespace Ephect\Modules\WebApp\Builder;

use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\PluginRegistry;
use Ephect\Modules\Forms\Registry\UniqueCodeRegistry;
use Ephect\Modules\Routing\Services\RouterService;
use Ephect\Modules\WebApp\Builder\Copiers\TemplatesCopyMaker;
use Ephect\Modules\WebApp\Builder\Descriptors\ComponentListDescriptor;
use Ephect\Modules\WebApp\Builder\Descriptors\ModuleListDescriptor;
use Ephect\Modules\WebApp\Builder\Descriptors\PluginListDescriptor;
use Ephect\Modules\WebApp\Builder\Descriptors\UniqueComponentDescriptor;
use Ephect\Modules\WebApp\Builder\Descriptors\UniqueComponentListDescriptor;
use Ephect\Modules\WebApp\Builder\Routing\Finder;
use Ephect\Modules\WebApp\Builder\Strategy\BuildByNameStrategy;
use Ephect\Modules\WebApp\Builder\Strategy\BuildByRouteStrategy;
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

            $copier = new TemplatesCopyMaker();
            $copier->makeCopies(true); // make unique copies

//            UniqueCodeRegistry::load();
//            $descriptor = new UniqueComponentListDescriptor();
//            $descriptor->describe();
//            UniqueCodeRegistry::save();
//
            CodeRegistry::load();

            $descriptor = new ComponentListDescriptor();
            $components = $descriptor->describe();
            $this->list = [...$this->list, ...$components];

            CodeRegistry::save();
            ComponentRegistry::save();
        }

        if (!PluginRegistry::load()) {
            [$filename, $modulePaths] = ModuleInstaller::readModulePaths();
            foreach ($modulePaths as $path) {
                if (str_starts_with($path, 'vendor')) {
                    $path = realpath(siteRoot() . $path);
                }
                $moduleSrcPathFile = $path . DIRECTORY_SEPARATOR . REL_CONFIG_DIR . REL_CONFIG_APP;
                $moduleSrcPath = file_exists($moduleSrcPathFile) ? $path . DIRECTORY_SEPARATOR . file_get_contents($moduleSrcPathFile) : $path . DIRECTORY_SEPARATOR . REL_CONFIG_APP;
                $moduleSrcPath = is_dir($moduleSrcPath) ? $moduleSrcPath : $path . DIRECTORY_SEPARATOR;


                $descriptor = new ModuleListDescriptor($path);
                $moduleComponents = $descriptor->describe($moduleSrcPath);
                $this->list = [...$this->list, ...$moduleComponents];
            }

            /**
             * Describe builtin modules
             */
            $descriptor = new PluginListDescriptor();
            $plugins = $descriptor->describe();
            $this->list = [...$this->list, ...$plugins];

            CodeRegistry::save();
            PluginRegistry::save();
            ComponentRegistry::save();
        }
    }

    public function prepareRoutedComponents(): void
    {
        CodeRegistry::load();
        ComponentRegistry::load();

        $routes = (new Finder())->searchForRoutes();

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
        (new BuildByNameStrategy())->build('App');
        $this->routes = RouterService::findRouteNames();

        $buildByRoute = new BuildByRouteStrategy();
        foreach ($this->routes as $route) {
            $buildByRoute->build($route);
        }
    }
}
