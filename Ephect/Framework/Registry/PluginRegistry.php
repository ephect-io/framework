<?php

namespace Ephect\Framework\Registry;

class PluginRegistry extends AbstractStaticRegistry
{
    private static ?AbstractRegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new PluginRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new PluginRegistry;
        }

        return self::$instance;
    }
}
