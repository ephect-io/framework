<?php

namespace Ephect\Modules\Routing\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

class HttpErrorRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new HttpErrorRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new HttpErrorRegistry;
        }

        return self::$instance;
    }
}
