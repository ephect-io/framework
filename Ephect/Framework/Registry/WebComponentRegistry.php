<?php

namespace Ephect\Framework\Registry;

class WebComponentRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new WebComponentRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new WebComponentRegistry;
        }

        return self::$instance;
    }
}
