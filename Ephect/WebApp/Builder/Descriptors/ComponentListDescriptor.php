<?php

namespace Ephect\WebApp\Builder\Descriptors;

use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Utils\File;

class ComponentListDescriptor implements ComponentListDescriptorInterface
{
    public function describe(string $templateDir = ''): array
    {
        $result = [];

        $descriptor = new ComponentDescriptor;

        $bootstrapList = File::walkTreeFiltered(SRC_ROOT, ['phtml'], true);
        foreach ($bootstrapList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe(SRC_ROOT, $compFile);
            $result[$fqcn] = $comp;
        }

        $pagesList = File::walkTreeFiltered(CUSTOM_PAGES_ROOT, ['phtml']);
        foreach ($pagesList as $key => $pageFile) {
            [$fqcn, $comp] = $descriptor->describe(CUSTOM_PAGES_ROOT, $pageFile);
            $result[$fqcn] = $comp;
        }

        $componentsList = File::walkTreeFiltered(CUSTOM_COMPONENTS_ROOT, ['phtml']);
        foreach ($componentsList as $key => $compFile) {
            [$fqcn, $comp] = $descriptor->describe(CUSTOM_COMPONENTS_ROOT, $compFile);
            $result[$fqcn] = $comp;
        }

        [$filename, $modulePaths] = ModuleInstaller::readModulePaths();
        foreach ($modulePaths as $path) {
            $moduleConfigDir = $path . DIRECTORY_SEPARATOR . REL_CONFIG_DIR;
            $moduleTemplatesFile = $moduleConfigDir . 'templates';
            $moduleTemplatesDir = trim(file_get_contents($moduleTemplatesFile));
            $configTemplatesDir = file_exists($moduleTemplatesFile) ? APP_ROOT . $moduleTemplatesDir : null;

            //TODO: Find the user's custom directory for the module's templates.
//            if ($configTemplatesDir !== null && !file_exists($configTemplatesDir)) {
//                $configTemplatesDir =  file_exists(CONFIG_DIR . 'webcomponents') ? trim(file_get_contents(CONFIG_DIR . 'webcomponents')) : $moduleTemplatesDir);
//            }

            if ($configTemplatesDir !== null && file_exists($configTemplatesDir)) {
                $componentsList = File::walkTreeFiltered($configTemplatesDir, ['phtml']);
                foreach ($componentsList as $key => $compFile) {
                    [$fqcn, $comp] = $descriptor->describe($configTemplatesDir, $compFile);
                    $result[$fqcn] = $comp;
                }
            }
        }

        return $result;
    }
}