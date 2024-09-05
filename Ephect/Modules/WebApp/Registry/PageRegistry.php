<?php

namespace Ephect\Modules\WebApp\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;

class PageRegistry extends AbstractStaticRegistry
{
    private static ?PageRegistry $instance = null;

    public static function reset(): void
    {
        self::$instance = new PageRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): PageRegistry
    {
        if (self::$instance === null) {
            self::$instance = new PageRegistry();
        }

        return self::$instance;
    }
}
