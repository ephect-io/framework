<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class HooksRegistry
{
    private static $instance = null;
    private static string $hooksFile = \Constants::RUNTIME_DIR . 'HooksRegistry.php';

    private function ___construct()
    {
    }

    public static function load(): void
    {
        self::create()->__load();
    }

    public static function register(string $path = \Constants::HOOKS_ROOT): void
    {
        self::create()->__register($path);
    }

    protected function __load(): void
    {
        if (!file_exists(self::$hooksFile)) {
            return;
        }

        $hooks = include self::$hooksFile;
        foreach ($hooks as $hook) {
            include_once $hook;
        }
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
            }

            if (file_exists($hooksFile) !== false) {
                $existingHooks = include $hooksFile;
                if (is_array($existingHooks)) {
                    $hooks = array_unique([...$existingHooks, ...$hooks]);
                }
            }

            $hooksRegistry = Text::jsonToPhpReturnedArray(json_encode($hooks));

            File::safeWrite($hooksFile, $hooksRegistry);
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
