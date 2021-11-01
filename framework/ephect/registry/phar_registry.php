<?php

namespace Ephect\Registry;

use Phar;

class PharRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new PharRegistry();
            // $runtime_dir = IS_PHAR_APP ? '' : RUNTIME_DIR;
            $runtime_dir = RUNTIME_DIR;
            self::$instance->_setCacheDirectory($runtime_dir);
        }

        return self::$instance;
    }

    public static function register(): void
    {
        FrameworkRegistry::uncache();
        $items = FrameworkRegistry::items();

        foreach($items as $key => $value) {

            $value = str_replace(FRAMEWORK_ROOT, '', $value);
            $value = str_replace(DIRECTORY_SEPARATOR, '_', $value);

            PharRegistry::write($key, $value);
        }

        PharRegistry::cache();
    }
}
