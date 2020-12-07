<?php

namespace FunCom\Registry;

class CacheRegistry extends AbstractStaticRegistry
{
    protected static $instance = null;

    public static function getInstance(): StaticRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new CacheRegistry();
        }

        return self::$instance;
    }
}
