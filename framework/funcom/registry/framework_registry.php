<?php

namespace FunCom\Registry;

class FrameworkRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new FrameworkRegistry();
            self::$instance->_setCacheDirectory(RUNTIME_DIR);
        }

        return self::$instance;
    }
}
