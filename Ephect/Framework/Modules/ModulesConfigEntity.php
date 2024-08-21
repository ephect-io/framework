<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Manifest\ManifestEntity;
use Ephect\Framework\Structure\StructureTrait;
use Override;

class ModulesConfigEntity extends ManifestEntity
{
    use StructureTrait;

    private array $modules = [];
    private array $modulesDev = [];

    public function __construct(?ModulesConfigStructure $structure = null)
    {
        $this->filename = siteRoot() . "modules.json";

        parent::__construct($structure);

        if ($structure instanceof ModulesConfigStructure) {
            $this->bindStructure($this->structure);
        }
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function getModulesDev(): array
    {
        return $this->modulesDev;
    }

    public function addModule(string $name, $version): void
    {
        $this->modules[$name] = $version;
    }

    public function removeModule(string $name): void
    {
        unset($this->modules[$name]);
    }

    #[Override]
    public function load(bool $asPhpArray = false): void
    {
        parent::load($asPhpArray);
        $this->structure = new ModulesConfigStructure($this->data);
        $this->bindStructure($this->structure);
    }

    #[Override]
    public function save(bool $asPhpArray = false): void
    {
        $this->structure = new ModulesConfigStructure(["modules" => $this->modules]);
        parent::save($asPhpArray);
    }

}