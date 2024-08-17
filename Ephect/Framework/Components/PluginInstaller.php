<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Modules\Composer\ComposerConfigEntity;
use Ephect\Framework\Modules\ModuleManifestReader;
use Ephect\Framework\Modules\ModulesConfigEntity;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\PluginRegistry;

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

        [$filename, $paths] = PluginRegistry::readPluginPaths();
        if(is_array($paths)) {
            $paths[] = $this->workingDirectory;
        }
        $paths = array_unique($paths);
        PluginRegistry::savePluginPaths($paths);

        [$filename, $paths] = PluginRegistry::readPluginBootstrapPaths();
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
        PluginRegistry::savePluginBootstrapPaths($paths);

        Console::writeLine("Plugin path %s is now declared.", $this->workingDirectory);

        $customClasses = FrameworkRegistry::collectCustomClasses($srcDir);

        foreach ($customClasses as $class => $filename) {
            FrameworkRegistry::write($class, $filename);
        }
        FrameworkRegistry::save(true);

        $moduleManifestReader = new ModuleManifestReader;
        $moduleManifest = $moduleManifestReader->read($configDir);

        $composerConfig = new ComposerConfigEntity;
        $composerConfig->load();

        $moduleConfig = new ModulesConfigEntity;
        $moduleConfig->load();

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

        [$filename, $paths] = PluginRegistry::readPluginPaths();
        if(is_array($paths)) {
            $paths = array_filter($paths, function ($path) use ($workingDirectory) {
                return $path !== $workingDirectory;
            });
        }

        PluginRegistry::savePluginPaths($paths);

        $srcDir = $this->workingDirectory . DIRECTORY_SEPARATOR . CONFIG_APP . DIRECTORY_SEPARATOR;
        $configDir = $this->workingDirectory . DIRECTORY_SEPARATOR . REL_CONFIG_DIR;

        $bootstrapFile = $srcDir . 'bootstrap.php';
        $constantsFile = $srcDir . 'constants.php';

        [$filename, $paths] = PluginRegistry::readPluginBootstrapPaths();
        if(is_array($paths)) {
            $paths = array_filter($paths, function ($path) use ($bootstrapFile, $constantsFile) {
                return $path !== $bootstrapFile && $path !== $constantsFile;
            });
        }

        PluginRegistry::savePluginBootstrapPaths($paths);

        Console::writeLine("Plugin path %s is now removed.", $workingDirectory);

        $customClasses =  FrameworkRegistry::collectCustomClasses($this->workingDirectory);

        foreach ($customClasses as $class => $filename) {
            FrameworkRegistry::delete($class);
        }
        FrameworkRegistry::save(true);

        $moduleManifestReader = new ModuleManifestReader;
        $moduleManifest = $moduleManifestReader->read($configDir);

        $moduleConfig = new ModulesConfigEntity;
        $moduleConfig->load();
        $moduleConfig->removeModule($moduleManifest->getName());
        $moduleConfig->save();

        Console::writeLine("Plugin classes are now unregistered.");

    }


}