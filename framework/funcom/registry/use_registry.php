<?php

namespace Ephect\Registry;

class UseRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new UseRegistry();
        }

        return self::$instance;
    }
}
