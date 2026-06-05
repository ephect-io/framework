<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\Structure\Structure;
use Closure;

class CommandOptionsStructure extends Structure
{
    public array $longArgs = [];
    public array $shortArgs = [];
}
