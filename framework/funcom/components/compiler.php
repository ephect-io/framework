<?php

namespace FunCom\Components;

use FunCom\Components\Generators\ComponentParser;
use FunCom\IO\Utils as IOUtils;
use FunCom\Registry\CodeRegistry;
use FunCom\Registry\PluginRegistry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;

class Compiler
{
    /** @return void  */
    public function perform(): void
    {
        if (!ViewRegistry::uncache()) {
            $viewList = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);
            foreach ($viewList as $key => $viewFile) {

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

            }
            CodeRegistry::cache();
            ViewRegistry::cache();
            UseRegistry::cache();
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
