<?php

namespace Ephect\Registry;

class CodeRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new CodeRegistry();
        }

        return self::$instance;
    }
}
