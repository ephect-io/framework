<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Element;
use Ephect\Framework\Manifest\ManifestEntity;

class ModuleManifestEntity extends ManifestEntity implements ModuleManifestEntityInterface
{
    private string $tag;
    private string $name;
    private string $entrypoint;
    private string $templates;

    public function __construct(ModuleManifestStructure $structure)
    {
        $this->tag = $structure->tag;
        $this->name = $structure->name;
        $this->entrypoint = $structure->entrypoint;
        $this->templates = $structure->templates;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEntrypoint(): string
    {
        return $this->entrypoint;
    }

    public function getTemplates(): string
    {
        return $this->templates;
    }

}