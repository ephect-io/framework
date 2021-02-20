<?php

namespace Ephect\Registry;

class CacheRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new CacheRegistry();
        }

        return self::$instance;
    }
}
