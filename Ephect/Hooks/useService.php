<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Services\ServiceFactory;
use Ephect\Framework\Services\ServiceFactoryInterface;
use Ephect\Framework\Services\ServiceInterface;

function useService(string $serviceClass): ?ServiceInterface
{
    $filename = CONFIG_DIR . "factories.php";
    if(!file_exists($filename)) {
        return null;
    }

    $result = null;

    $factories = require_once $filename;
    foreach ($factories as $factoryClass) {
        $factoryFile = FrameworkRegistry::read($factoryClass);
        include_once  $factoryFile;
        if (is_subclass_of($factoryClass, ServiceFactoryInterface::class)) {
            $factory = new $factoryClass;
            $result = $factory->create($serviceClass);
            break;
        }
    }

    return  $result;
}