<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\Registry\FrameworkRegistry;
use Exception;

class Autoloader
{
    /**
     * Registers the autoloader class with the PHP SPL autoloader.
     *
     * @param bool $prepend Prepend the autoloader on the stack instead of appending it.
     */
    public static function register(bool $prepend = false): void
    {
        spl_autoload_register(array(new self(), 'load'), true, $prepend);
    }

    public static function load($className): void
    {
        $classFilename = FrameworkRegistry::read($className);

        /**
         * Only handle framework classes - let other autoloaders handle other classes
         */
        if (empty($classFilename)) {
            // Only throw exception for framework classes, let other autoloaders handle the rest
            if (str_starts_with($className, 'Ephect\\') || str_starts_with($className, 'DevRez\\')) {
                throw new Exception("Class $className not found");
            }
            return; // Let other autoloaders try
        }

        include $classFilename;
    }
}
