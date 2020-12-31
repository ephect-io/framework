<?php

namespace FunCom\Core;

use FunCom\Registry\FrameworkRegistry;

class Autoloader
{

    /**
     * Registers the autoloader class with the PHP SPL autoloader.
     *
     * @param bool $prepend Prepend the autoloader on the stack instead of appending it.
     */
    public static function register($prepend = false)
    {
        spl_autoload_register(array(new self, 'load'), true, $prepend);
    }

    public static function load($className): void
    {
        $classFilename =  FrameworkRegistry::read($className);

        include FRAMEWORK_ROOT . $classFilename;
    }
}

