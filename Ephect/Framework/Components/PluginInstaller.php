<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class PluginInstaller
{
    public function __construct(private string $workingDirectory)
    {

    }
    public function install(): void
    {
        [$filename, $paths] = $this->readPluginPaths();

        if(is_array($paths)) {
            $paths[] = $this->workingDirectory;
        }
        $paths = array_unique($paths);

        $this->savePluginPaths($filename, $paths);

        Console::writeLine("Plugin path %s is now declared.", $this->workingDirectory);
    }

    public function remove(): void
    {
        $workingDirectory = $this->workingDirectory;
        [$filename, $paths] = $this->readPluginPaths();
        if(is_array($paths)) {
            $paths = array_filter($paths, function ($path) use ($workingDirectory) {
                return $path !== $workingDirectory;
            });
        }

        $this->savePluginPaths($filename, $paths);

        Console::writeLine("Plugin path %s is now removed.", $workingDirectory);
    }

    private function readPluginPaths(): array
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

    private function savePluginPaths(string $filename, array $paths): void
    {
        $json = json_encode($paths);
        $pluginsPaths = Text::jsonToPhpReturnedArray($json, true);

        File::safeWrite($filename, $pluginsPaths);
    }
}