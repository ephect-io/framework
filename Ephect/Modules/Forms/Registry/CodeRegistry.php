<?php

namespace Ephect\Modules\Forms\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

class CodeRegistry extends AbstractStaticRegistry
{
    private static ?CodeRegistry $instance = null;

    public static function reset(): void
    {
        self::$instance = new CodeRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): CodeRegistry
    {
        if (self::$instance === null) {
            self::$instance = new CodeRegistry();
        }

        return self::$instance;
    }
}
