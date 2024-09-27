<?php

namespace Ephect\Modules\Forms\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

class UniqueCodeRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new UniqueCodeRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new UniqueCodeRegistry();
        }

        return self::$instance;
    }
}
