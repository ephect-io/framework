<?php

namespace Ephect\Framework\Modules\Composer;

use Ephect\Framework\Structure\JsonProperty;
use Ephect\Framework\Structure\Structure;

class ComposerConfigStructure extends Structure
{
    public string $name = '';

    public string $type = '';

    public string $homepage = '';

    public string $license = '';

    public string $description = '';

    public array $authors = [];

    public array $autoload = [];

    #[JsonProperty(name: "minimum-stability")]
    public string $minimumStability = '';

    public array $require = [];

    #[JsonProperty(name: "require-dev")]
    public array $requireDev = [];
}