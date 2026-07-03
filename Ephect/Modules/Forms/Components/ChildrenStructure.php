<?php

namespace Ephect\Modules\Forms\Components;

class ChildrenStructure extends ComponentProps
{
    public ?object $props = null;
    public string $class = '';
    public string $name = '';
    public ?array $parentProps = null;
    public string $motherUID = '';
    public ?object $buffer = null;
}
