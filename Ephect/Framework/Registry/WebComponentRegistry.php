<?php

namespace Ephect\Framework\Registry;

class WebComponentRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void {
        self::$instance = new WebComponentRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new WebComponentRegistry;
        }

        return self::$instance;
    }
}
