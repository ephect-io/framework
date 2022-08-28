<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\IO\Utils;

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

    public static function register(): void
    {
        self::create()->_register();
    }

    protected function _register(): void
    {
        if (!IS_PHAR_APP) {

            $hooks = [];
            $dir_handle = opendir(EPHECT_ROOT . HOOKS_PATH);
        
            while (false !== $filename = readdir($dir_handle)) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }
        
                array_push($hooks, str_replace(DIRECTORY_SEPARATOR, '_' , HOOKS_PATH . $filename));
        
                include EPHECT_ROOT . HOOKS_PATH . $filename;
            }
        
            $hooksRegistry = ['Hooks' => $hooks];
        
            Utils::safeWrite(RUNTIME_DIR . 'HooksRegistry.json',  json_encode($hooksRegistry));
        }
        
        if (IS_PHAR_APP) {
            $hooksRegistry = Utils::safeRead(RUNTIME_DIR . 'HooksRegistry.json');
        
            $hooks = json_decode($hooksRegistry);
            $hooks = $hooks->hooks;
            
            foreach($hooks as $hook) {
                include $hook;
            }
        }
    }
}
