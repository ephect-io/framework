<?php

namespace Ephect\Modules\Forms\Components;

use Ephect\Framework\Structure\Structure;

class ComponentDeclarationStructure extends Structure
{
    public string $uid = '';
    public string $className = '';
    public ?string $returnType = null;
    public ?string $name = '';
    public ?array $arguments = [];
    public ?array $argumentsTypes = [];
    public ?array $attributes = [];
    public ?array $composition = null;
}
