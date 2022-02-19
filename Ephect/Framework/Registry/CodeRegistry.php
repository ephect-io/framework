<?php

namespace Ephect\Framework\Registry;

class CodeRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void {
        self::$instance = new CodeRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new CodeRegistry;
        }

        return self::$instance;
    }
}
