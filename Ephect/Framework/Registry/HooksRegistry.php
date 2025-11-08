<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class HooksRegistry
{
    private static $instance = null;

    private function ___construct()
    {
    }

    public static function register(string $path = \Constants::EPHECT_ROOT . \Constants::HOOKS_DIR): void
    {
        self::create()->__register($path);
    }

    protected function __register(string $path): void
    {
        $hooksFile = \Constants::RUNTIME_DIR . 'HooksRegistry.php';

        if (!\Constants::IS_PHAR_APP) {
            if (!file_exists($path)) {
                return;
            }

            $hooks = [];
            $dir_handle = opendir($path);

            while (false !== $filename = readdir($dir_handle)) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }

                $hooks[] = str_replace(
                    DIRECTORY_SEPARATOR,
                    PHP_OS === 'WINNT' ? DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR  : DIRECTORY_SEPARATOR,
                    $path . $filename
                );

                if(file_exists($path . DIRECTORY_SEPARATOR . $filename)) {
                    include $path . DIRECTORY_SEPARATOR . $filename;
                }
            }

            $hooksRegistry = Text::jsonToPhpReturnedArray(json_encode($hooks));

            File::safeWrite($hooksFile, $hooksRegistry);
        }

        if (\Constants::IS_PHAR_APP) {
            $hooksRegistry = File::safeRead($hooksFile);

            $hooks = json_decode($hooksRegistry);
            $hooks = $hooks->hooks;

            foreach ($hooks as $hook) {
                include $hook;
            }
        }
    }

    public static function create(): HooksRegistry
    {
        if (self::$instance === null) {
            self::$instance = new HooksRegistry();
        }

        return self::$instance;
    }
}
