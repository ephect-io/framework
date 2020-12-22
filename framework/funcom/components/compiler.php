<?php

namespace FunCom\Components;

use FunCom\IO\Utils as IOUtils;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\CacheRegistry;
use FunCom\Registry\UseRegistry;
use FunCom\Registry\ViewRegistry;

class Compiler
{
    /** @return void  */
    public function perform(): void
    {
        $viewList = $this->searchForViews();

        ViewRegistry::uncache();

        $views = [];

        foreach ($viewList as $viewFile) {

            $view = new View();
            $view->load($viewFile);
            $view->analyse();

            array_push($views, $view);

            ViewRegistry::write($viewFile, $view->getUID());
        }
        ViewRegistry::cache();
        ClassRegistry::cache();
        UseRegistry::cache();

    }

    /** @return array  */
    private function searchForViews(): array
    {
        $result = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);

        return $result;
    }


}
