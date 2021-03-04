<?php

namespace Ephect\Components;

use Ephect\Components\Generators\BlocksParser;
use Ephect\Components\Generators\ComponentParser;
use Ephect\IO\Utils as IOUtils;
use Ephect\Registry\CodeRegistry;
use Ephect\Registry\PluginRegistry;
use Ephect\Registry\UseRegistry;
use Ephect\Registry\ViewRegistry;

class Compiler
{
    /** @return void  */
    public function perform(): void
    {
        if (!ViewRegistry::uncache()) {
            $viewList = [];
            $templateList = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);
            foreach ($templateList as $key => $viewFile) {

                $view = new View();
                $view->load($viewFile);
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
                ViewRegistry::write($viewFile, $view->getUID());

                array_push($viewList, $view);
            }
            CodeRegistry::cache();
            ViewRegistry::cache();
            UseRegistry::cache();
        }

        foreach($viewList as $view) {
            $parser = new BlocksParser($view);
            $parser->doBlocks();
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
            UseRegistry::cache();
        }
    }

}
