<?php

namespace FunCom\Registry;

class ViewRegistry extends AbstractStaticRegistry
{
    protected static $instance = null;

    public static function getInstance(): StaticRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new ViewRegistry();
        }

        return self::$instance;
    }
}
