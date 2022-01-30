<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\Core\Structure;

class ComponentDeclarationStructure extends Structure
{
    public $uid = '';
    public $type = '';
    public $name = '';
    public $arguments = [];
    public $composition = null;

}