<?php

namespace Ephect\Framework\Components\Application;

use Ephect\Framework\Components\ComponentInterface;
use Ephect\Framework\Registry\ComponentRegistry;
use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Utils\File;

class ComponentsCopier
{
    public static function copy(array &$list, ?string $motherUID = null, ?ComponentInterface $component = null): ?string
    {
        $cachedir = CACHE_DIR . $motherUID . DIRECTORY_SEPARATOR;
        $componentList = $component->composedOf();
        $copyFile = $component->getSourceFilename();
        $copyPath = pathinfo($copyFile, PATHINFO_DIRNAME);

        File::safeMkDir($cachedir . $copyPath);

        if ($componentList === null) {
            if (!file_exists($cachedir . $copyFile)) {
                copy(COPY_DIR . $copyFile, $cachedir . $copyFile);
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
            $component->copyComponents($list, $motherUID, $nextComponent);
        }

        if (!file_exists($cachedir . $copyFile)) {
            copy(COPY_DIR . $copyFile, $cachedir . $copyFile);
        }

        return $copyFile;
    }
}