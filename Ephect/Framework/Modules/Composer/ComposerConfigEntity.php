<?php

namespace Ephect\Framework\Modules\Composer;

use Ephect\Framework\Manifest\ManifestEntity;
use Ephect\Framework\Structure\StructureTrait;

class ComposerConfigEntity extends ManifestEntity
{

    use StructureTrait;

    private string $name;

    private string $type;

    private string $homepage;

    private string $license;

    private string $description;

    private array $authors;

    private array $autoload;

    private string $minimumStability;

    private array $require = [];

    private array $requireDev = [];

    public function __construct(?ComposerConfigStructure $structure = null)
    {
        $this->filename = siteRoot() . "composer.json";

        parent::__construct($structure);

        if($structure instanceof ComposerConfigStructure) {
            $this->bindStructure($this->structure);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getHomepage(): string
    {
        return $this->homepage;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function getAutoload(): array
    {
        return $this->autoload;
    }

    public function getMinimumStability(): string
    {
        return $this->minimumStability;
    }

    public function getRequire(): array
    {
        return $this->require;
    }

    public function getRequireDev(): array
    {
        return $this->requireDev;
    }

    private function bindStructure_()
    {
        $this->name = $this->structure->name;
        $this->type = $this->structure->type;
        $this->homepage = $this->structure->homepage;
        $this->license = $this->structure->license;
        $this->description = $this->structure->description;
        $this->authors = $this->structure->authors;
        $this->autoload = $this->structure->autoload;
        $this->minimumStability = $this->structure->minimumStability;
        $this->require = $this->structure->require;
        $this->requireDev = $this->structure->requireDev;
    }

    #[\Override]
    public function load(bool $asPhpArray = false): void
    {
        parent::load($asPhpArray);
        $this->structure = new ComposerConfigStructure($this->data);
        $this->bindStructure($this->structure);
    }

    #[\Override]
    public function save(bool $asPhpArray = false): void
    {
        // DO NOT SAVE composer.json
    }
}