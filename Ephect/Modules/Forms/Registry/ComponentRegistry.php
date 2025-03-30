<?php

namespace Ephect\Modules\Forms\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

class ComponentRegistry extends AbstractStaticRegistry
{
    private static ?ComponentRegistry $instance = null;

    public static function reset(): void
    {
        self::$instance = new ComponentRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): ComponentRegistry
    {
        if (self::$instance === null) {
            self::$instance = new ComponentRegistry();
        }

        return self::$instance;
    }
}
