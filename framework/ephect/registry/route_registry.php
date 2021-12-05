<?php

namespace Ephect\Registry;

class RouteRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void {
        self::$instance = new RouteRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new RouteRegistry;
        }

        return self::$instance;
    }
}
