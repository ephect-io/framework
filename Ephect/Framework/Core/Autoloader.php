<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\Registry\FrameworkRegistry;

class Autoloader
{

    /**
     * Registers the autoloader class with the PHP SPL autoloader.
     *
     * @param bool $prepend Prepend the autoloader on the stack instead of appending it.
     */
    public static function register(bool $prepend = false): void
    {
        spl_autoload_register(array(new self, 'load'), true, $prepend);
    }

    public static function load($className): void
    {
        $classFilename = FrameworkRegistry::read($className);

        /**
         * Activate only for debug
         */
        if (empty($classFilename)) {
            throw new \Exception("Class $className not found");
        }

        include $classFilename;
    }
}
