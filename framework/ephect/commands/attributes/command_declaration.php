<?php

namespace Ephect\Commands\Attributes;

use Attribute;

#[Attribute]
class CommandDeclaration
{
    public function __construct(
        public string $long = '',
        public string $short = '',
        public string $desc = '',
        public bool $isPhar = false
    ) {
    }
}
