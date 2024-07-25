<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class PluginInstaller
{
    public static function install(string $workingDirectory)
    {
        $filename = CONFIG_DIR . "pluginsPaths.php";

        $paths = [];
        if(is_file($filename)) {
            $paths = require $filename;
        }

        if(is_array($paths)) {
            $paths[] = $workingDirectory;
        }

        $pluginsPaths = Text::jsonToPhpReturnedArray($paths, true);
        File::safeWrite($filename, $pluginsPaths);
    }
}