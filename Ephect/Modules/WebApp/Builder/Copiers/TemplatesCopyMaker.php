<?php

namespace Ephect\Modules\WebApp\Builder\Copiers;

use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Utils\File;
use Ephect\Modules\WebApp\Builder\Copiers\Strategy\CopiersFactory;
use Ephect\Modules\WebApp\Builder\Copiers\Strategy\CopyModulesPagesStrategy;

class TemplatesCopyMaker
{
    public function makeCopies(bool $asUnique = false)
    {
        $copier = CopiersFactory::createCopier($asUnique);

        TemplatesCopier::copy($copier, \Constants::SRC_ROOT, true);
        TemplatesCopier::copy($copier, \Constants::CUSTOM_PAGES_ROOT);
        TemplatesCopier::copy($copier, \Constants::CUSTOM_COMPONENTS_ROOT);
    }

    public function makeModulesPageCopies()
    {
        $copier = new CopyModulesPagesStrategy();

        [$filename, $modulePaths] = ModuleInstaller::readModulePaths();
        foreach ($modulePaths as $path) {
            $moduleSrcPath = ModuleInstaller::normalizeModuleSrcPath($path);
            $pagePath = $moduleSrcPath . 'Forms' . DIRECTORY_SEPARATOR . 'Pages';
            if(!is_dir($pagePath)) {
                continue;
            }

            TemplatesCopier::copy($copier, $pagePath);
        }    
    }
}
