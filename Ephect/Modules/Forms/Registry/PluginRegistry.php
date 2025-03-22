<?php

namespace Ephect\Modules\Forms\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

class PluginRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new PluginRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractStaticRegistry
    {
        if (self::$instance === null) {
            self::$instance = new PluginRegistry;
        }

        return self::$instance;
    }


}
