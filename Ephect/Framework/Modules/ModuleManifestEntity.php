<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Manifest\ManifestEntity;
use Ephect\Framework\Structure\StructureTrait;

class ModuleManifestEntity extends ManifestEntity implements ModuleManifestEntityInterface
{
    use StructureTrait;

    private string $tag = '';
    private string $name = '';
    private ?string $entrypoint = null;
    private string $templates = '';
    private string $description = '';
    private string $version = '';

    public function __construct(ModuleManifestStructure $structure)
    {
        parent::__construct($structure);
        $this->bindStructure($structure);
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEntrypoint(): ?string
    {
        return $this->entrypoint;
    }

    public function getTemplates(): string
    {
        return $this->templates;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

}
