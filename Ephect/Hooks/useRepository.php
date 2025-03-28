<?php

namespace Ephect\Hooks;

use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Repositories\RepositoryFactory;
use Ephect\Framework\Repositories\RepositoryFactoryInterface;
use Ephect\Framework\Repositories\RepositoryInterface;

function useRepository(string $repositoryClass): ?RepositoryInterface
{
    $filename = \Constants::CONFIG_DIR . "factories.php";
    if (!file_exists($filename)) {
        return null;
    }

    $result = null;

    $factories = require_once $filename;
    foreach ($factories as $factoryClass) {
        $factoryFile = FrameworkRegistry::read($factoryClass);
        include_once $factoryFile;
        if (is_subclass_of($factoryClass, RepositoryFactoryInterface::class)) {
            $factory = new $factoryClass;
            $result = $factory->create($repositoryClass);
            break;
        }
    }

    return $result;
}