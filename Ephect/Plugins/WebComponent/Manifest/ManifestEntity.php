<?php

namespace Ephect\Plugins\WebComponent\Manifest;

use Ephect\Framework\Element;

class ManifestEntity extends Element implements ManifestEntityInterface
{
    private string $tag;
    private string $className;
    private string $entrypoint;
    private array $arguments;

    public function __construct(ManifestStructure $structure)
    {
        $this->tag = $structure->tag;
        $this->className = $structure->class;
        $this->entrypoint = $structure->entrypoint;
        $this->arguments = $structure->arguments;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getEntrypoint(): string
    {
        return $this->entrypoint;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

}