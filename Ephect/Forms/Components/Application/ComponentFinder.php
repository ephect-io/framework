<?php

namespace Ephect\Forms\Components\Application;

use BadFunctionCallException;
use Ephect\Forms\Registry\CacheRegistry;
use Ephect\Forms\Registry\ComponentRegistry;

class ComponentFinder
{
    public static function find(string $componentName, string $motherUID): array
    {
        ComponentRegistry::load();
        $uses = ComponentRegistry::items();
        $fqFuncName = $uses[$componentName] ?? null;

        if ($fqFuncName === null) {
            throw new BadFunctionCallException('The component ' . $componentName . ' does not exist.');
        }

        CacheRegistry::load();

        if ($motherUID === '') {
            $filename = $uses[$fqFuncName];
            $motherUID = $uses[$filename];
        }
        $filename = CacheRegistry::read($motherUID, $fqFuncName);
        $filename = ($filename !== null) ? $motherUID . DIRECTORY_SEPARATOR . $filename : $filename;
        $isCached = $filename !== null;

        return [$fqFuncName, $filename, $isCached];
    }
}