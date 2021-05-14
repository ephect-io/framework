<?php

namespace Ephect\Components;

use Ephect\Core\Structure;

class ComponentDeclarationStructure extends Structure
{
    public $type = '';
    public $name = '';
    public $arguments = [];
    public $composition = null;

}