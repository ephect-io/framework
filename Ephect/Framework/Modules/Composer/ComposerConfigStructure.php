<?php

namespace Ephect\Framework\Modules\Composer;

use Ephect\Framework\Structure\JsonProperty;
use Ephect\Framework\Structure\Structure;

class ComposerConfigStructure extends Structure
{
    public array $require = [];

    #[JsonProperty(name: "require-dev")]
    public array $requireDev = [];
}