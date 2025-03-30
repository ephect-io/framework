<?php

namespace Ephect\Modules\Routing\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

class HttpErrorRegistry extends AbstractStaticRegistry
{
    private static ?HttpErrorRegistry $instance = null;

    public static function reset(): void
    {
        self::$instance = new HttpErrorRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): HttpErrorRegistry
    {
        if (self::$instance === null) {
            self::$instance = new HttpErrorRegistry();
        }

        return self::$instance;
    }
}
