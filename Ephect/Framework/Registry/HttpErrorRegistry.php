<?php

namespace Ephect\Framework\Registry;

class HttpErrorRegistry extends AbstractStaticRegistry
{
    private static ?AbstractRegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new HttpErrorRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new HttpErrorRegistry;
        }

        return self::$instance;
    }
}
