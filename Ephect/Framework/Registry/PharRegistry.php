<?php

namespace Ephect\Framework\Registry;

use Phar;

class PharRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void { 
        self::$instance = new PharRegistry;
        self::$instance->_setCacheDirectory(RUNTIME_DIR);
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new PharRegistry;
            self::$instance->_setCacheDirectory(RUNTIME_DIR);
        }

        return self::$instance;
    }

    public static function register(): void
    {
        FrameworkRegistry::uncache(true);
        $items = FrameworkRegistry::items();

        foreach($items as $key => $value) {

            $value = str_replace(EPHECT_ROOT, '', $value);
            $value = str_replace(DIRECTORY_SEPARATOR, '_', $value);

            PharRegistry::write($key, $value);
        }

        PharRegistry::cache();
    }
}
