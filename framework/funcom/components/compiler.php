<?php

namespace FunCom\Components;

use FunCom\IO\Utils as IOUtils;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\UseRegistry;

class Compiler
{
    public function perform(): void
    {
        $viewList = $this->searchForViews();

        foreach ($viewList as $viewFile) {
            $view = new View();
            $view->load($viewFile);
            $view->parse();
        }

        ClassRegistry::cache();
        UseRegistry::cache();
    }

    private function searchForViews(): array
    {
        $result = IOUtils::walkTreeFiltered(SRC_ROOT, ['phtml']);

        return $result;
    }


}
