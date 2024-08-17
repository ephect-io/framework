<?php

namespace Ephect\Framework\Modules\Composer;

use Ephect\Framework\Entity\Entity;
use Ephect\Framework\Manifest\ManifestEntity;

class ComposerConfigEntity extends ManifestEntity
{
    private array $require = [];
    private array $requireDev = [];

    public function __construct(?ComposerConfigStructure $structure = null)
    {
        $this->filename = siteRoot() . "composer.json";

        parent::__construct($structure);

        if($structure instanceof ComposerConfigStructure) {
            $this->bindStructure();
        }
    }

    public function getRequire(): array
    {
        return $this->require;
    }

    public function getRequireDev(): array
    {
        return $this->requireDev;
    }

    private function bindStructure()
    {
        $this->require = $this->structure->require;
        $this->requireDev = $this->structure->requireDev;
    }

    #[\Override]
    public function load(bool $asPhpArray = false): void
    {
        parent::load($asPhpArray);
        $this->structure = new ComposerConfigStructure($this->data);
        $this->bindStructure();
    }

    #[\Override]
    public function save(bool $asPhpArray = false): void
    {
        // DO NOT SAVE composer.json
    }
}