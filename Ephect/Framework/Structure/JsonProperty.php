<?php

namespace Ephect\Framework\Structure;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class JsonProperty
{
    public function __construct(
        private string $name,
    )
    {
    }
}


