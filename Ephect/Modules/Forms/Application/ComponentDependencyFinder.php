<?php

namespace Ephect\Modules\Forms\Application;

use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\FileComponentInterface;
use Ephect\Modules\Forms\Registry\ComponentRegistry;
use Ephect\Modules\Forms\Registry\PluginRegistry;

class ComponentDependencyFinder
{
    public static function find(array &$list, ?string $motherUID = null, ?FileComponentInterface $component = null): ?string
    {
        $cachedir = \Constants::BUILD_DIR . $motherUID . DIRECTORY_SEPARATOR;
        $componentList = $component->composedOf();
        $copyFile = $component->getSourceFilename();
        $copyPath = pathinfo($copyFile, PATHINFO_DIRNAME);

        File::safeMkDir($cachedir . $copyPath);

        if ($componentList === null) {
            if (!file_exists($cachedir . $copyFile)) {
                copy(\Constants::COPY_DIR . $copyFile, $cachedir . $copyFile);
            }

            return $copyFile;
        }

        $fqFuncName = $component->getFullyQualifiedFunction();
        foreach ($componentList as $entity) {
            $funcName = $entity->getName();
            $fqFuncName = ComponentRegistry::read($funcName);

            if ($fqFuncName === null) {
                continue;
            }
            $nextComponent = !isset($list[$fqFuncName]) ? null : $list[$fqFuncName];

            $nextCopyFile = '';
            if ($nextComponent !== null) {
                $nextCopyFile = $nextComponent->getSourceFilename();
            }

            if ($nextComponent === null) {
                $nextCopyFile = PluginRegistry::read($fqFuncName);
            }
            if (file_exists($cachedir . $nextCopyFile)) {
                continue;
            }

            if ($nextComponent === null) {
                continue;
            }
            $component->findDependencies($list, $motherUID, $nextComponent);
        }

        return $copyFile;
    }
}
