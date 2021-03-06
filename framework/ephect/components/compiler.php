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

            $compList = [];
            $templateList = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);
            foreach ($templateList as $key => $compFile) {

                $cachedSourceViewFile = Component::getCacheFilename('source_' . $compFile);
                copy(SRC_ROOT . $compFile, CACHE_DIR . $cachedSourceViewFile);

                $comp = new Component();
                $comp->load($cachedSourceViewFile);
                $comp->analyse();

                $parser = new ComponentParser($comp);
                $list = $parser->doComponents();

                $compose = new Composition($comp->getFullyQualifiedFunction());

                foreach ($list as $item) {
                    $entity = new ComponentEntity(new ComponentStructure($item));
                    $compose->items()->add($entity);
                }

                $compose->bindNodes();

                $composition = $compose->toArray();

                CodeRegistry::write($comp->getFullyQualifiedFunction(), $composition);
                ComponentRegistry::write($cachedSourceViewFile, $comp->getUID());

                array_push($compList, $comp);
            }
            CodeRegistry::cache();
            ComponentRegistry::cache();

            $blocksViews = [];
            foreach ($compList as $comp) {
                $parser = new BlocksParser($comp);
                $filename = $parser->doBlocks();

                if($filename !== null && file_exists(SRC_COPY_DIR . $filename)) {
                    array_push($blocksViews, $filename);
                }
            }

            if(count($blocksViews) > 0) {
                foreach ($blocksViews as $compFile) {
                    $comp = new Component();
                    $comp->load($compFile);
                    $comp->analyse();

                    ComponentRegistry::write($compFile, $comp->getUID());

                }

            }
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
