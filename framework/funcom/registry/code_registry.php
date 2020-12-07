<?php

namespace FunCom\Registry;

class CodeRegistry extends AbstractStaticRegistry
{
    protected static $instance = null;

    public static function getInstance(): StaticRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new CodeRegistry();
        }

        return self::$instance;
    }
}
