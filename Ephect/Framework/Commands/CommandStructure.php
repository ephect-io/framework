<?php

namespace Ephect\Framework\Commands;

use Closure;
use Ephect\Framework\Core\Structure;

class CommandStructure extends Structure
{
    public string $subject = '';
    public string $verb = '';
    public string $desc = '';
    public bool $isPhar = false;
    public $callback = null;
}