<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\Logger\Logger;
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
        spl_autoload_register(array(new self(), 'load'), true, $prepend);
    }

    public static function load($className): void
    {
        $classFilename = FrameworkRegistry::read($className);
        if (empty($classFilename)) {
            Logger::create()->debug("Autoloader: Class `$className` not found in registry");
            return;
        }

        include $classFilename;
    }
}
