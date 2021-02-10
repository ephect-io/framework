<?php

namespace FunCom\Components;

use FunCom\Core\Structure;

class ComponentStructure extends Structure
{
    public $id;
    public $view;
    public $name;
    public $method;
    public $text;
    public $parentId;
    public $depth;
    public $startsAt;
    public $endsAt;
    public $props;
    public $closer;
    // public $isCloser;
    public $isSibling;
    public $hasCloser;
}
