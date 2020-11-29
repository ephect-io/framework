<?php

namespace FunCom\Components;

use FunCom\IO\Utils as IOUtils;
use FunCom\Registry\ClassRegistry;

class Compiler
{

    public function perform(): void
    {
        $viewList = $this->searchForViews();

        foreach ($viewList as $viewFile) {
            $view = new View();
            $view->load($viewFile);

            $html = $view->getCode();

            $parser = new Parser($html);
            $parser->doVariables();
            $parser->doComponents();
            $html = $parser->getHtml();

            $cacheFilename = $this->cacheView($viewFile, $html);
        
            ClassRegistry::write($view->getFullCleasName(), $cacheFilename);
        }

        ClassRegistry::cache();
    }

    private function searchForViews(): array
    {
        $result = IOUtils::walkTree(SRC_ROOT, ['phtml']);

        return $result;
    }

    private function cacheView($filename, $contents): ?string
    {
        $cache_file = REL_CACHE_DIR . str_replace('/', '_', $filename);

        $result = IOUtils::safeWrite(SITE_ROOT . $cache_file, $contents);

        return $result === null ? $result : $cache_file;
    }
}
