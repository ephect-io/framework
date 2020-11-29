<?php

namespace FunCom\Core;

use FunCom\Components\Compiler;

abstract class Application extends Element
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run(?array ...$params) : void
    {
    }
}
