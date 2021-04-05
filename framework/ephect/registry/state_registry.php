<?php

namespace Ephect\Registry;

class StateRegistry extends AbstractStaticRegistryContract
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new StateRegistry();
        }

        return self::$instance;
    }
}
