<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Utils\File;

class HooksRegistry
{
    private static $instance = null;

    private function ___construct()
    {
    }

    public static function register(string $path = \Constants::EPHECT_ROOT): void
    {
        self::create()->__register($path);
    }

    protected function __register(string $path = \Constants::EPHECT_ROOT): void
    {
        if (!\Constants::IS_PHAR_APP) {
            if (!file_exists($path . \Constants::HOOKS_DIR)) {
                return;
            }
            $hooks = [];
            $dir_handle = opendir($path . \Constants::HOOKS_DIR);

            while (false !== $filename = readdir($dir_handle)) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }

                $hooks[] = str_replace(
                    DIRECTORY_SEPARATOR,
                    PHP_OS === 'WINNT' ? DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR  : DIRECTORY_SEPARATOR,
                    \Constants::HOOKS_DIR . DIRECTORY_SEPARATOR . $filename
                );

                include $path . \Constants::HOOKS_DIR . DIRECTORY_SEPARATOR . $filename;
            }

            $hooksRegistry = ['Hooks' => $hooks];

            File::safeWrite(\Constants::RUNTIME_DIR . 'HooksRegistry.json', json_encode($hooksRegistry));
        }

        if (\Constants::IS_PHAR_APP) {
            $hooksRegistry = File::safeRead(\Constants::RUNTIME_DIR . 'HooksRegistry.json');

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
