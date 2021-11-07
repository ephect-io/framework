<?php

namespace Ephect\Commands\Attributes;

use Attribute;

#[Attribute]
class CommandDeclaration
{
    public function __construct(
        public string $verb = '',
        public string $subject = '',
        public string $desc = '',
        public bool $isPhar = false
    ) {
    }
}
