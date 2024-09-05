<?php

namespace Ephect\Framework\Registry;

class MiddlewareRegistry extends AbstractStaticRegistry
{
    private static ?MiddlewareRegistry $instance = null;

    public static function reset(): void
    {
        self::$instance = new MiddlewareRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): MiddlewareRegistry
    {
        if (self::$instance === null) {
            self::$instance = new MiddlewareRegistry();
        }

        return self::$instance;
    }
}
