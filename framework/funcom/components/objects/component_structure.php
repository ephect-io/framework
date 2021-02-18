<?php

namespace FunCom\Components;

use FunCom\Core\Structure;

class ComponentStructure extends Structure
{
    public $uid;
    public $id;
    public $class;
    public $view;
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
