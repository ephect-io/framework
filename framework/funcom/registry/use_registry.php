<?php

namespace FunCom\Registry;

class UseRegistry extends AbstractStaticRegistry
{
    protected static $instance = null;

    public static function getInstance(): StaticRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new UseRegistry();
        }

        return self::$instance;
    }
}
