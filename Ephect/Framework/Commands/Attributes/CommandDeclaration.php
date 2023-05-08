<?php

namespace Ephect\Framework\Commands\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class CommandDeclaration
{
    public function __construct(
        public string $verb = '',
        public string $subject = '',
        public string $desc = '',
        public bool   $isPhar = false
    )
    {
    }
}
