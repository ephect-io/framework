<?php

namespace FunCom\Registry;

class PluginRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new PluginRegistry();
        }

        return self::$instance;
    }
}
