<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Core\Structure;

class ComponentStructure extends Structure
{
    public string $uid = '';
    public string $motherUID = '';
    public string $id = '';
    public ?string $class = '';
    public string $component = '';
    public string $name = '';
    public string $method = '';
    public string $text = '';
    public string $parentId = '';
    public int $depth = 0;
    public int $startsAt = 0;
    public int $endsAt = 0;
    public ?bool $isSibling = false;
    public bool $hasCloser = false;
    public ?array $closer = [];
    public array $props = [];
    public bool|array|null $node = null;
}
