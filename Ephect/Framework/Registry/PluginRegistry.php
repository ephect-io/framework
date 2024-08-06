<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class PluginRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new PluginRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractStaticRegistry
    {
        if (self::$instance === null) {
            self::$instance = new PluginRegistry;
        }

        return self::$instance;
    }

    public static function readPluginPaths(): array
    {
        $configDir = applicationConfigPath();

        $filename = $configDir . "pluginsPaths.php";

        $paths = [];
        if(is_file($filename)) {
            $paths = require $filename;
        }

        return [$filename, $paths];
    }

    public static function savePluginPaths(array $paths): void
    {
        $configDir = applicationConfigPath();

        $filename = $configDir . "pluginsPaths.php";

        $json = json_encode($paths);
        $pluginsPaths = Text::jsonToPhpReturnedArray($json, true);

        File::safeWrite($filename, $pluginsPaths);
    }
}
