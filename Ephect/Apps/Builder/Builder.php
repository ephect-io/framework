<?php

namespace Ephect\Apps\Builder;

use DateTime;
use Ephect\Apps\Builder\Copiers\FilesCopier;
use Ephect\Apps\Builder\Descriptors\ComponentsDescriptor;
use Ephect\Apps\Builder\Descriptors\PluginsDescriptor;
use Ephect\Apps\Builder\Descriptors\WebComponentsDescriptor;
use Ephect\Apps\Builder\Routes\Finder;
use Ephect\Apps\Builder\Strategy\BuildByNameStrategy;
use Ephect\Apps\Builder\Strategy\BuildByRouteStrategy;
use Ephect\Framework\Registry\CodeRegistry;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Registry\WebComponentRegistry;
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

            $copier = new FilesCopier;

//            $copier->makeCopies();
            $copier->makeCopies(true); // make unique copies

            CodeRegistry::load();

            $descriptor = new ComponentsDescriptor;

            $bootstrapList = File::walkTreeFiltered(SRC_ROOT, ['phtml'], true);
            foreach ($bootstrapList as $key => $compFile) {
                [$fqcn, $comp] = $descriptor->describe(SRC_ROOT, $compFile);
                $this->list[$fqcn] = $comp;
            }

            $pagesList = File::walkTreeFiltered(CUSTOM_PAGES_ROOT, ['phtml']);
            foreach ($pagesList as $key => $pageFile) {
                [$fqcn, $comp] = $descriptor->describe(CUSTOM_PAGES_ROOT, $pageFile);
                $this->list[$fqcn] = $comp;
            }

            $componentsList = File::walkTreeFiltered(CUSTOM_COMPONENTS_ROOT, ['phtml']);
            foreach ($componentsList as $key => $compFile) {
                [$fqcn, $comp] = $descriptor->describe(CUSTOM_COMPONENTS_ROOT, $compFile);
                $this->list[$fqcn] = $comp;
            }

            CodeRegistry::save();
            ComponentRegistry::save();
        }

        if (!PluginRegistry::load()) {
            $descriptor = new PluginsDescriptor;
            $pluginList = File::walkTreeFiltered(PLUGINS_ROOT, ['phtml']);
            foreach ($pluginList as $key => $pluginFile) {
                $descriptor->describe(PLUGINS_ROOT, $pluginFile);
            }
            PluginRegistry::save();
            ComponentRegistry::save();
        }

        if (file_exists(CUSTOM_WEBCOMPONENTS_ROOT)) {
            if (!WebComponentRegistry::load()) {
                $descriptor = new WebComponentsDescriptor;
                $webcomponentList = File::walkTreeFiltered(CUSTOM_WEBCOMPONENTS_ROOT, ['phtml']);
                foreach ($webcomponentList as $key => $webcomponentFile) {
                    [$fqcn, $comp] = $descriptor->describe(CUSTOM_WEBCOMPONENTS_ROOT, $webcomponentFile);
                    $this->list[$fqcn] = $comp;
                }
                CodeRegistry::save();
                WebComponentRegistry::save();
                ComponentRegistry::save();
            }
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
