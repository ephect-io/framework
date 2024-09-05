<?php

namespace Ephect\Framework\Modules;


use Ephect\Framework\Manifest\ManifestStructure;

class ModuleManifestStructure extends ManifestStructure
{
    public string $tag = '';
    public string $name = '';
    public ?string $entrypoint = '';
    public string $templates = 'templates';
    public string $description = '';
    public string $version = '';
}
