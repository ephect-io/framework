<?php

namespace Ephect\Modules\WebApp\Builder\Finders;

use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Utils\File;

class PagesFinder implements FinderInterface
{
    public function find(): array
    {
        $result = [];

        $pagesList = File::walkTreeFiltered(\Constants::CUSTOM_PAGES_ROOT, ['phtml']);
        foreach ($pagesList as $key => $pageFile) {
            $result[] = $pageFile;
        }

        [$filename, $modulePaths] = ModuleInstaller::readModulePaths();
        foreach ($modulePaths as $path) {
            $moduleSrcPath = ModuleInstaller::normalizeModuleSrcPath($path);
            $pagePath = $moduleSrcPath . 'Forms' . DIRECTORY_SEPARATOR . 'Pages';
            if(!is_dir($pagePath)) {
                continue;
            }
            $pagesList = File::walkTreeFiltered($pagePath, ['phtml']);
            if ($pagesList === false) {
                continue;
            }
            foreach ($pagesList as $key => $pageFile) {
                $result[] = $pageFile;
            }
        }
        return $result;
    }
}
