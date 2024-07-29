<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class PluginInstaller
{
    public static function install(string $workingDirectory): void
    {
        [$filename, $paths] = self::readPluginPaths();

        if(is_array($paths)) {
            $paths[] = $workingDirectory;
        }
        $paths = array_unique($paths);

        self::savePluginPaths($filename, $paths);

        Console::writeLine("Plugin path %s is now declared.", $workingDirectory);
    }

    public static function remove(string $workingDirectory): void
    {
        [$filename, $paths] = self::readPluginPaths();
        if(is_array($paths)) {
            $paths = array_filter($paths, function ($path) use ($workingDirectory) {
                return $path !== $workingDirectory;
            });
        }

        self::savePluginPaths($filename, $paths);

        Console::writeLine("Plugin path %s is now removed.", $workingDirectory);
    }

    private static function readPluginPaths(): array
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

        return [$filename, $paths];
    }

    private static function savePluginPaths(string $filename, array $paths): void
    {
        $json = json_encode($paths);
        $pluginsPaths = Text::jsonToPhpReturnedArray($json, true);

        File::safeWrite($filename, $pluginsPaths);
    }
}