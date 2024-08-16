<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Structure\Structure;

class ChildrenStructure extends Structure
{
    public string $uid = '';
    public ?object $props = null;
    public string $class = '';
    public string $name = '';
    public ?array $parentProps = null;
    public string $motherUID = '';
    public ?object $buffer = null;
}
