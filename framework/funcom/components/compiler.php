<?php

namespace FunCom\Components;

use FunCom\ElementUtils;
use FunCom\IO\Utils as IOUtils;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\PluginRegistry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;

class Compiler
{
    /** @return void  */
    public function perform(): void
    {
        if (!ViewRegistry::uncache()) {
            $viewList = $this->searchForViews();
            foreach ($viewList as $key => $viewFile) {

                $view = new View();
                $view->load($viewFile);
                $view->analyse();

                ViewRegistry::write($viewFile, $view->getUID());
            }
            ViewRegistry::cache();
            ClassRegistry::cache();
            UseRegistry::cache();
        }

        if (!PluginRegistry::uncache()) {
            $pluginList = $this->searchForPlugins();
            foreach ($pluginList as $key => $pluginFile) {
                // list($ns, $class) = ElementUtils::getClassDefinitionFromFile(PLUGINS_ROOT . $pluginFile);
                // $fqClass = $ns . '\\' . $class;
                // $plugin = new $fqClass;
                $plugin = new Plugin();
                $plugin->load($pluginFile);
                $plugin->analyse();

                PluginRegistry::write($pluginFile, $plugin->getUID());
            }
            PluginRegistry::cache();
            UseRegistry::cache();
        }
    }

    /** @return array  */
    private function searchForViews(): array
    {
        $result = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);

        return $result;
    }


    /** @return array  */
    private function searchForPlugins(): array
    {
        $result = IOUtils::walkTreeFiltered(PLUGINS_ROOT, ['php']);

        return $result;
    }
}
