<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Core\Structure;

class ChildrenStructure extends Structure
{
    public string $uid = '';
    public ?object $props = null;
    public string $class = '';
    public ?array $parentProps = null;
    public string $motherUID = '';
    public ?object $onrender = null;
}
