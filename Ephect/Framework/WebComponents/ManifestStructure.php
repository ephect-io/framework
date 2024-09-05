<?php

namespace Ephect\Framework\WebComponents;

use Ephect\Framework\Core\Structure;

class ManifestStructure extends Structure
{
    public string $tag = '';
    public string $class = '';
    public string $entrypoint = '';
    public array $arguments = [];
}
