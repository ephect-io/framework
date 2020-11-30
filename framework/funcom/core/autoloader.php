<?php

namespace FunCom\Core;

use FunCom\Registry\ClassRegistry;

class Autoloader
{
    private $directory;
    private $prefix;
    private $prefixLength;

    /**
     * @param string $baseDirectory Base directory where the source files are located.
     */
    public function __construct($baseDirectory = __DIR__)
    {
        $this->directory = $baseDirectory;
        $this->prefix = __NAMESPACE__ . '\\';
        $this->prefixLength = strlen($this->prefix);

        ClassRegistry::uncache();
    }

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
        $classFilename = ClassRegistry::read($className);

        include SITE_ROOT . $className;
    }
}


new Autoloader(SITE_ROOT);