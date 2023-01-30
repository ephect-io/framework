<?php

namespace Ephect\Framework\Webcomponents;

class Manifest
{
    private $tag;
    private $class;
    private $file;

    public function __construct(ManifestStructure $structure)
    {
        $this->tag = $structure->tag;
        $this->class = $structure->class;
        $this->file = $structure->file;
    }

}