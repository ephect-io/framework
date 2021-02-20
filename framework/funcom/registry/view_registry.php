<?php

namespace Ephect\Registry;

class ViewRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new ViewRegistry();
        }

        return self::$instance;
    }
}
