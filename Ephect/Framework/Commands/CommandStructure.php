<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\Structure\Structure;

class CommandStructure extends Structure
{
    public string $subject = '';
    public string $verb = '';
    public string $desc = '';
    public bool $isPhar = false;
    public $callback = null;
}