<?php

namespace Ephect\Forms\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

class ComponentRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new ComponentRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new ComponentRegistry;
        }

        return self::$instance;
    }
}
