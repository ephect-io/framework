<?php

namespace Ephect\Framework\Modules;

use Ephect\Framework\Structure\JsonProperty;
use Ephect\Framework\Structure\Structure;

class ModulesConfigStructure extends Structure
{
    public array $modules = [];

    #[JsonProperty(name: "modules-dev")]
    public array $modulesDev = [];
}
