<?php

namespace Ephect\Core;

use Ephect\Element;

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
