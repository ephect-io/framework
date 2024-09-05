<?php

namespace Ephect\Framework\Registry;

class EventRegistry extends AbstractStaticRegistry
{
    private static ?EventRegistry $instance = null;

    public static function reset(): void
    {
        self::$instance = new EventRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): EventRegistry
    {
        if (self::$instance === null) {
            self::$instance = new EventRegistry();
        }

        return self::$instance;
    }
}
