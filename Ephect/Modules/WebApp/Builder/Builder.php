<?php

namespace Ephect\Modules\WebApp\Builder;

use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Registry\CodeRegistry;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\PluginRegistry;
use Ephect\Modules\Routing\Services\RouterService;
use Ephect\Modules\WebApp\Builder\Copiers\TemplatesCopyMaker;
use Ephect\Modules\WebApp\Builder\Descriptors\ComponentListDescriptor;
use Ephect\Modules\WebApp\Builder\Descriptors\ModuleListDescriptor;
use Ephect\Modules\WebApp\Builder\Descriptors\PluginListDescriptor;
use Ephect\Modules\WebApp\Builder\Finders\PagesFinder;
use Ephect\Modules\WebApp\Builder\Finders\RoutesFinder;
use Ephect\Modules\WebApp\Builder\Registerer\PageRegisterer;
use Ephect\Modules\WebApp\Builder\Strategy\BuildByNameStrategy;
use Ephect\Modules\WebApp\Builder\Strategy\BuildByRouteStrategy;
use Exception;

class Builder
{
    protected array $list = [];
    protected array $routes = [];

    public static function purgeCopies(): void
    {
        File::delTree(\Constants::PROCESS_DIR);
    }

    public function getListOfComponents(): array
    {
        return $this->list;
    }
    
    /**
     * Register all components of the application
     *
     * @return void
     */
    public function describeComponents(): void
    {
        if (!ComponentRegistry::load()) {
            File::safeMkDir(\Constants::CACHE_DIR);
            File::safeMkDir(\Constants::PROCESS_DIR);
            File::safeMkDir(\Constants::COPY_DIR);
            File::safeMkDir(\Constants::BUILD_DIR);
            File::safeMkDir(\Constants::STATIC_DIR);

            $copier = new TemplatesCopyMaker();
            $copier->makeCopies(true); // make unique copies
            $copier->makeCopies(); // make copies as is
            $copier->makeModulesPageCopies(); // make modules page copies

            //            UniqueCodeRegistry::load();
            //            $descriptor = new UniqueComponentListDescriptor();
            //            $descriptor->describe();
            //            UniqueCodeRegistry::save();

            CodeRegistry::load();

            $descriptor = new ComponentListDescriptor();
            $components = $descriptor->describe();
            $this->list = [...$this->list, ...$components];

            //            CodeRegistry::save();
            //            ComponentRegistry::save();
        }

        if (!PluginRegistry::load()) {
            [$filename, $modulePaths] = ModuleInstaller::readModulePaths();
            foreach ($modulePaths as $path) {
                $moduleSrcPath = ModuleInstaller::normalizeModuleSrcPath($path);
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

    public function preparePagesList(): void
    {
        $pagesFinder = new PagesFinder();
        $pagesList = $pagesFinder->find();

        $pagesRegisterer = new PageRegisterer();
        $pagesRegisterer->register($pagesList);
    }

    /**
     * This looks useless unril it's not
     */
    // public function prepareRoutedComponents(): void
    // {
    //     CodeRegistry::load();
    //     ComponentRegistry::load();

    //     $routes = (new RoutesFinder())->find();
    //     // TODO: check if it works
    //     //        $fqApp = ComponentRegistry::read('App');
    //     $fqApp = 'App';

    //     array_unshift($routes, $fqApp);

    //     foreach ($routes as $route) {
    //         $fqRoute = ComponentRegistry::read($route);
    //         $comp = $this->list[$fqRoute];

    //         // $comp->copyComponents($this->list);
    //     }

    //     $this->routes = $routes;
    // }

    /**
     * @throws Exception
     */
    public function buildAllRoutes(): void
    {
        (new BuildByNameStrategy())->build('App');
        // TODO: check if it works
        $this->routes = RouterService::findRouteNames();

        $buildByRoute = new BuildByRouteStrategy();
        foreach ($this->routes as $route) {
            $buildByRoute->build($route);
        }
    }

    public function buildAllPages(): void
    {
        (new BuildByNameStrategy())->build('App');
        // TODO: check if it works
        $this->routes = RouterService::findRouteNames();

        $buildByRoute = new BuildByNameStrategy();
        foreach ($this->routes as $route) {
            $buildByRoute->build($route);
        }
    }

    public function buildAnyByName(array $pageNames): void
    {
        $buildByName = new BuildByNameStrategy();
        foreach ($pageNames as $page) {
            $buildByName->build($page);
        }
    }  

}
