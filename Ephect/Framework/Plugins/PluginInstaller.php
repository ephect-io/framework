<?php

namespace Ephect\Framework\Plugins;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Modules\Composer\ComposerConfigReader;
use Ephect\Framework\Modules\ModulesConfigReader;
use Ephect\Framework\Modules\ModuleManifestReader;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class PluginInstaller
{
    public function __construct(private string $workingDirectory)
    {

    }

    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    public function install(): void
    {
        FrameworkRegistry::load(true);
        $srcDir = $this->workingDirectory . DIRECTORY_SEPARATOR . CONFIG_APP . DIRECTORY_SEPARATOR;
        $configDir = $this->workingDirectory . DIRECTORY_SEPARATOR . REL_CONFIG_DIR;

        [$filename, $paths] = self::readPluginPaths();
        if(is_array($paths)) {
            $paths[] = $this->workingDirectory;
        }
        $paths = array_unique($paths);
        self::savePluginPaths($paths);

        [$filename, $paths] = self::readPluginBootstrapPaths();
        if(is_array($paths)) {
            $bootstrapFile = $srcDir . 'bootstrap.php';
            if(file_exists($bootstrapFile)) {
                $paths[] = $bootstrapFile;
            }
            $constantsFile = $srcDir . 'constants.php';
            if(file_exists($constantsFile)) {
                $paths[] = $constantsFile;
            }
        }
        $paths = array_unique($paths);
        self::savePluginBootstrapPaths($paths);

        Console::writeLine("Plugin path %s is now declared.", $this->workingDirectory);

        $customClasses = FrameworkRegistry::collectCustomClasses($srcDir);

        foreach ($customClasses as $class => $filename) {
            FrameworkRegistry::write($class, $filename);
        }
        FrameworkRegistry::save(true);

        $moduleManifestReader = new ModuleManifestReader;
        $moduleManifest = $moduleManifestReader->read($configDir);

        $composerConfigReader = new ComposerConfigReader;
        $composerConfig = $composerConfigReader->read();

        $moduleConfigReader = new ModulesConfigReader;
        $moduleConfig = $moduleConfigReader->read();

        $requires = $composerConfig->getRequire();
        $package = $moduleManifest->getName();
        $version = $moduleManifest->getVersion();
        foreach ($requires as $requireName => $requireVersion) {
            if($package == $requireName && $requireVersion !== $version && !empty($requireVersion)) {
                $moduleConfig->addModule($package, $requireVersion);
            } else {
                $moduleConfig->addModule($package, $moduleManifest->getVersion());
            }
        }

        $moduleConfig->save();

        Console::writeLine("Plugin classes are now registered.");

    }

    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    public function remove(): void
    {
        FrameworkRegistry::load(true);
        $workingDirectory = $this->workingDirectory;

        [$filename, $paths] = self::readPluginPaths();
        if(is_array($paths)) {
            $paths = array_filter($paths, function ($path) use ($workingDirectory) {
                return $path !== $workingDirectory;
            });
        }

        self::savePluginPaths($paths);

        $srcDir = $this->workingDirectory . DIRECTORY_SEPARATOR . CONFIG_APP . DIRECTORY_SEPARATOR;
        $configDir = $this->workingDirectory . DIRECTORY_SEPARATOR . REL_CONFIG_DIR;

        $bootstrapFile = $srcDir . 'bootstrap.php';
        $constantsFile = $srcDir . 'constants.php';

        [$filename, $paths] = self::readPluginBootstrapPaths();
        if(is_array($paths)) {
            $paths = array_filter($paths, function ($path) use ($bootstrapFile, $constantsFile) {
                return $path !== $bootstrapFile && $path !== $constantsFile;
            });
        }

        self::savePluginBootstrapPaths($paths);

        Console::writeLine("Plugin path %s is now removed.", $workingDirectory);

        $customClasses =  FrameworkRegistry::collectCustomClasses($this->workingDirectory);

        foreach ($customClasses as $class => $filename) {
            FrameworkRegistry::delete($class);
        }
        FrameworkRegistry::save(true);

        $moduleManifestReader = new ModuleManifestReader;
        $moduleManifest = $moduleManifestReader->read($configDir);

        $moduleConfigReader = new ModulesConfigReader;
        $moduleConfig = $moduleConfigReader->read();
        $moduleConfig->removeModule($moduleManifest->getName());
        $moduleConfig->save();

        Console::writeLine("Plugin classes are now unregistered.");

    }

    public static function readPluginPaths(): array
    {
        $configDir = siteConfigPath();
        $filename = $configDir . "pluginsPaths.php";

        $paths = [];
        if(file_exists($filename)) {
            $paths = require $filename;
        }

        return [$filename, $paths];
    }

    public static function savePluginPaths(array $paths): void
    {
        $configDir = siteConfigPath();
        $filename = $configDir . "pluginsPaths.php";

        $json = json_encode($paths);
        $pluginsPaths = Text::jsonToPhpReturnedArray($json, true);

        File::safeWrite($filename, $pluginsPaths);
    }

    public static function readPluginBootstrapPaths(): array
    {
        $configDir = siteConfigPath();
        $filename = $configDir . "pluginsBootstrapPaths.php";

        $paths = [];
        if(file_exists($filename)) {
            $paths = require $filename;
        }

        return [$filename, $paths];
    }

    public static function savePluginBootstrapPaths(array $paths): void
    {
        $configDir = siteConfigPath();
        $filename = $configDir . "pluginsBootstrapPaths.php";

        $json = json_encode($paths);
        $pluginsPaths = Text::jsonToPhpReturnedArray($json, true);

        File::safeWrite($filename, $pluginsPaths);
    }

    public static function loadBootstraps(): void
    {
        [$filename, $paths] = self::readPluginBootstrapPaths();
        foreach ($paths as $path) {
            require $path;
        }
    }
}