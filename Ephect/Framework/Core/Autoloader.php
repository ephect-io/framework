<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\PharRegistry;

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
        $classFilename = (IS_PHAR_APP) ? PharRegistry::read($className) : FrameworkRegistry::read($className);

        include $classFilename;
    }
}
