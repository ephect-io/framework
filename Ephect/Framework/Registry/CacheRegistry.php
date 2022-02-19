<?php

namespace Ephect\Framework\Registry;

class CacheRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void { 
        self::$instance = new CacheRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new CacheRegistry();
        }

        return self::$instance;
    }
}
