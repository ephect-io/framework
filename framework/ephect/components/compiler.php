<?php

namespace Ephect\Components;

use Ephect\Cache\Cache;
use Ephect\Components\Generators\BlocksParser;
use Ephect\Components\Generators\ComponentParser;
use Ephect\IO\Utils as IOUtils;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\PluginRegistry;

class Compiler
{
    /** @return void  */
    public function perform(): void
    {
        if (!ComponentRegistry::uncache()) {
            Cache::createCacheDir();

            $viewList = [];
            $templateList = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);
            foreach ($templateList as $key => $viewFile) {

                $cachedSourceViewFile = View::getCacheFilename('source_' . $viewFile);
                copy(SRC_ROOT . $viewFile, CACHE_DIR . $cachedSourceViewFile);

                $view = new View();
                $view->load($cachedSourceViewFile);
                $view->analyse();

                $parser = new ComponentParser($view);
                $list = $parser->doComponents();

                $compose = new Composition($view->getFullyQualifiedFunction());

                foreach ($list as $item) {
                    $entity = new ComponentEntity(new ComponentStructure($item));
                    $compose->items()->add($entity);
                }

                $compose->bindNodes();

                $composition = $compose->toArray();

                CodeRegistry::write($view->getFullyQualifiedFunction(), $composition);
                ComponentRegistry::write($cachedSourceViewFile, $view->getUID());

                array_push($viewList, $view);
            }
            CodeRegistry::cache();
            ComponentRegistry::cache();

            // $blocksViews = [];
            // foreach ($viewList as $view) {
            //     $parser = new BlocksParser($view);
            //     $filename = $parser->doBlocks();

            //     if($filename !== null && file_exists(SRC_COPY_DIR . $filename)) {
            //         array_push($blocksViews, $filename);
            //     }
            // }

            // if(count($blocksViews) > 0) {
            //     foreach ($blocksViews as $viewFile) {
            //         $view = new View();
            //         $view->load($viewFile);
            //         $view->analyse();

            //         ComponentRegistry::write($viewFile, $view->getUID());

            //     }

            // }
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
}
