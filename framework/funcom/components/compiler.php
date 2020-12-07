<?php

namespace FunCom\Components;

use FunCom\IO\Utils as IOUtils;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\UseRegistry;

class Compiler
{
    public function perform(): void
    {
        $viewList = $this->searchForViews();

        $views = [];

        foreach ($viewList as $viewFile) {
            $view = new View();
            $view->load($viewFile);
            $view->analyse();

            array_push($views, $view);
        }

        CacheRegistry::cache();
        ClassRegistry::cache();
        UseRegistry::cache();

        $dummy = 0;
        
        foreach($views as $view) {
            $view->parse();
        }

    }

    private function searchForViews(): array
    {
        $result = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);

        return $result;
    }


}
