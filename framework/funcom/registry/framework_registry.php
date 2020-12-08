<?php

namespace FunCom\Registry;

class FrameworkRegistry extends AbstractStaticRegistry
{
    protected static $instance = null;

    public static function getInstance(): StaticRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new FrameworkRegistry();
        }

        return self::$instance;
    }
}
