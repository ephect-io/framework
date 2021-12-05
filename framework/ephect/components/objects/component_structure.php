<?php

namespace Ephect\Components;

use Ephect\Core\Structure;

class ComponentStructure extends Structure
{
    public $uid;
    public $motherUID;
    public $id;
    public $class;
    public $component;
    public $name;
    public $method;
    public $text;
    public $parentId;
    public $depth;
    public $startsAt;
    public $endsAt;
    public $isSibling;
    public $hasCloser;
    public $closer;
    public $props;
    public $node;
}
