<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class PluginInstaller
{
    public static function install(string $workingDirectory)
    {
        $vendorPos = strpos( CONFIG_DIR, 'vendor');
        $configDir = CONFIG_DIR;

        if($vendorPos > -1) {
            $configDir = substr(CONFIG_DIR, 0, $vendorPos) . 'config' . DIRECTORY_SEPARATOR;
        }

        $filename = $configDir . "pluginsPaths.php";

        $paths = [];
        if(is_file($filename)) {
            $paths = require $filename;
        }

        if(is_array($paths)) {
            $paths[] = $workingDirectory;
        }

        $paths = array_unique($paths);

        $json = json_encode($paths);
        $pluginsPaths = Text::jsonToPhpReturnedArray($json, true);

        File::safeWrite($filename, $pluginsPaths);

        Console::writeLine("Plugin path %s is now declared.", $workingDirectory);
    }
}