<?php

namespace Ephect\Forms\Components;

use Ephect\Framework\Structure\Structure;

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
    public int $parentId = 0;
    public int $depth = 0;
    public int $startsAt = 0;
    public int $endsAt = 0;
    public bool $isSibling = false;
    public bool $hasCloser = false;
    public ?array $closer = null;
    public array $props = [];
    public false|array $node = [];
    public bool $isSingle = false;
}
