<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Core\Structure;

class ComponentDeclarationStructure extends Structure
{
    public string $uid = '';
    public string $type = '';
    public string $name = '';
    public array $arguments = [];
    public ?array $composition = null;

}