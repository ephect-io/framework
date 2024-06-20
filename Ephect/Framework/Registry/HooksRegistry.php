<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Utils\File;

class HooksRegistry
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function create(): HooksRegistry
    {
        if (self::$instance === null) {
            self::$instance = new HooksRegistry;
        }

        return self::$instance;
    }

    public static function register(string $path = EPHECT_ROOT): void
    {
        self::create()->_register($path);
    }

    protected function _register(string $path = EPHECT_ROOT): void
    {
        if (!IS_PHAR_APP) {

            if (!file_exists($path . HOOKS_DIR)) {
                return;
            }
            $hooks = [];
            $dir_handle = opendir($path . HOOKS_DIR);

            while (false !== $filename = readdir($dir_handle)) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }

                array_push($hooks, str_replace(DIRECTORY_SEPARATOR, '_' , HOOKS_DIR . $filename));

                include $path . HOOKS_DIR . $filename;
            }

            $hooksRegistry = ['Hooks' => $hooks];

            File::safeWrite(RUNTIME_DIR . 'HooksRegistry.json', json_encode($hooksRegistry));
        }

        if (IS_PHAR_APP) {
            $hooksRegistry = File::safeRead(RUNTIME_DIR . 'HooksRegistry.json');

            $hooks = json_decode($hooksRegistry);
            $hooks = $hooks->hooks;

            foreach ($hooks as $hook) {
                include $hook;
            }
        }
    }

}
