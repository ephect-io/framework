<?php

namespace Ephect\Framework\Registry;

class WebcomponentRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void {
        self::$instance = new WebcomponentRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new WebcomponentRegistry;
        }

        return self::$instance;
    }
}
