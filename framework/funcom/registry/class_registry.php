<?php

namespace FunCom\Registry;

class ClassRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new ClassRegistry();
        }

        return self::$instance;
    }
}
