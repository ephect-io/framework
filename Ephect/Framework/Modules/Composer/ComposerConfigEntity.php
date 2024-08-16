<?php

namespace Ephect\Framework\Modules\Composer;

use Ephect\Framework\Entity\Entity;

class ComposerConfigEntity extends Entity
{
    private array $require = [];

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

    private function bindStructure()
    {
        $this->require = $this->structure->require;
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

    }
}