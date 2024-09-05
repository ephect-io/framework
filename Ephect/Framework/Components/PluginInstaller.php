<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\PluginRegistry;

class PluginInstaller
{
    public function __construct(private string $workingDirectory)
    {

    }

    public function install(): void
    {
        FrameworkRegistry::load(true);
        $srcDir = $this->workingDirectory . DIRECTORY_SEPARATOR . CONFIG_APP . DIRECTORY_SEPARATOR;

        [$filename, $paths] = PluginRegistry::readPluginPaths();
        if (is_array($paths)) {
            $paths[] = $this->workingDirectory;
        }
        $paths = array_unique($paths);
        PluginRegistry::savePluginPaths($paths);

        [$filename, $paths] = PluginRegistry::readPluginBootstrapPaths();
        if (is_array($paths)) {
            $bootstrapFile = $srcDir . 'bootstrap.php';
            if (file_exists($bootstrapFile)) {
                $paths[] = $bootstrapFile;
            }
            $constantsFile = $srcDir . 'constants.php';
            if (file_exists($constantsFile)) {
                $paths[] = $constantsFile;
            }
        }
        $paths = array_unique($paths);
        PluginRegistry::savePluginBootstrapPaths($paths);

        Console::writeLine("Plugin path %s is now declared.", $this->workingDirectory);

        $customClasses = FrameworkRegistry::collectCustomClasses($srcDir);

        foreach ($customClasses as $class => $filename) {
            FrameworkRegistry::write($class, $filename);
        }
        FrameworkRegistry::save(true);

        Console::writeLine("Plugin classes are now registered.");

    }

    public function remove(): void
    {
        FrameworkRegistry::load(true);
        $workingDirectory = $this->workingDirectory;

        [$filename, $paths] = PluginRegistry::readPluginPaths();
        if (is_array($paths)) {
            $paths = array_filter($paths, function ($path) use ($workingDirectory) {
                return $path !== $workingDirectory;
            });
        }

        PluginRegistry::savePluginPaths($paths);

        $srcDir = $this->workingDirectory . DIRECTORY_SEPARATOR . CONFIG_APP . DIRECTORY_SEPARATOR;
        $bootstrapFile = $srcDir . 'bootstrap.php';
        $constantsFile = $srcDir . 'constants.php';

        [$filename, $paths] = PluginRegistry::readPluginBootstrapPaths();
        if (is_array($paths)) {
            $paths = array_filter($paths, function ($path) use ($bootstrapFile, $constantsFile) {
                return $path !== $bootstrapFile && $path !== $constantsFile;
            });
        }

        PluginRegistry::savePluginBootstrapPaths($paths);

        Console::writeLine("Plugin path %s is now removed.", $workingDirectory);

        $customClasses = FrameworkRegistry::collectCustomClasses($this->workingDirectory);

        foreach ($customClasses as $class => $filename) {
            FrameworkRegistry::delete($class);
        }
        FrameworkRegistry::save(true);

        Console::writeLine("Plugin classes are now unregistered.");

    }


}