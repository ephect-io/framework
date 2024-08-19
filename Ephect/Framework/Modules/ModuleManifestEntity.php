<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Element;
use Ephect\Framework\Manifest\ManifestEntity;

class ModuleManifestEntity extends ManifestEntity implements ModuleManifestEntityInterface
{
    private string $tag = '';
    private string $name = '';
    private ?string $entrypoint = null;
    private string $templates = '';
    private string $description = '';
    private string $version = '';

    public function __construct(ModuleManifestStructure $structure)
    {
        parent::__construct($structure);

        $this->tag = $structure->tag;
        $this->name = $structure->name;
        $this->entrypoint = $structure->entrypoint;
        $this->templates = $structure->templates;
        $this->description = $structure->description;
        $this->version = $structure->version;
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