<?php

namespace Ephect\Framework\Modules;

class Utils
{
    public function __construct(private string $currentDirectory)
    {
    }

    public function getModuleDir()
    {
        $moduleConfigDir = dirname($this->currentDirectory, 2) . DIRECTORY_SEPARATOR . \Constants::REL_CONFIG_DIR;

        return (is_dir($moduleConfigDir) ?
            dirname($this->currentDirectory, 2) : dirname($this->currentDirectory)) . DIRECTORY_SEPARATOR;
    }

    public function getModuleSrcDir()
    {
        return dirname($this->currentDirectory) . DIRECTORY_SEPARATOR;
    }

    public function getModuleConfDir()
    {
        $moduleConfigDir = dirname($this->currentDirectory, 2) . DIRECTORY_SEPARATOR . \Constants::REL_CONFIG_DIR;
        return is_dir($moduleConfigDir) ? $moduleConfigDir : dirname($this->currentDirectory) . DIRECTORY_SEPARATOR;
    }

    public function getModuleManifest(): ModuleManifestEntity
    {
        $manifestReader = new ModuleManifestReader();
        return $manifestReader->read($this->getModuleConfDir());
    }
}
