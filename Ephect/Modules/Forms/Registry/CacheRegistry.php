<?php

namespace Ephect\Modules\Forms\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

class CacheRegistry extends AbstractStaticRegistry
{
    private static ?CacheRegistry $instance = null;

    public static function reset(): void
    {
        self::$instance = new CacheRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): CacheRegistry
    {
        if (self::$instance === null) {
            self::$instance = new CacheRegistry();
        }

        return self::$instance;
    }
}
