<?php

namespace Ephect\Registry;

class ComponentRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void 
    { 
        self::$instance = new ComponentRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new ComponentRegistry;
        }

        return self::$instance;
    }
}
