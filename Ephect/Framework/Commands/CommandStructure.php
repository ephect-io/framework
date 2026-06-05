<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\Structure\Structure;
use Closure;

class CommandStructure extends Structure
{
    public string $subject = '';
    public string $verb = '';
    public string $desc = '';
    public array $longArgs = [];
    public array $shortArgs = [];
    public bool $isPhar = false;
    public ?CommandInterface $callback = null;
}
