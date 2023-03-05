<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\Element;

class Manifest extends Element
{
    private string $tag;
    private string $class;
    private string $entrypoint;
    private array $arguments;

    public function __construct(ManifestStructure $structure)
    {
        $this->tag = $structure->tag;
        $this->class = $structure->class;
        $this->entrypoint = $structure->entrypoint;
        $this->arguments = $structure->arguments;
    }

}