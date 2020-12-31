<?php

namespace FunCom\Core;

use FunCom\Element;

abstract class AbstractApplication extends Element
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run(?array ...$params) : void
    {
    }
}
