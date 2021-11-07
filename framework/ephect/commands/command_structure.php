<?php

namespace Ephect\Commands;

use Closure;
use Ephect\Core\Structure;

class CommandStructure extends Structure
{
    public string $long = '';
    public string $short = '';
    public string $desc = '';
    public bool $isPhar = false;
    public $callback = null;
}