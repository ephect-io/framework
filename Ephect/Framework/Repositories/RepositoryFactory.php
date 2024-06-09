<?php

namespace Ephect\Framework\Repositories;

use Ephect\Framework\Registry\FrameworkRegistry;

final class RepositoryFactory implements RepositoryFactoryInterface
{
    public function create(string $repositoryClass): RepositoryInterface|null
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
            if (is_subclass_of($factoryClass,RepositoryFactoryInterface::class)) {
                $factory = new $factoryClass;
                $result = $factory->create($repositoryClass);
                break;
            }
        }

        return  $result;
    }
}

