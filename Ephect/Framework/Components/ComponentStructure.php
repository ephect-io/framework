<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Core\Structure;

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
    public $isCloser;
    public $hasCloser;
    public $closer;
    public $props;
    public $node;
}
