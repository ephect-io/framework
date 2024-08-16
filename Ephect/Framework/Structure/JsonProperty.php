<?php

namespace Ephect\Framework\Structure;

use Attribute;

#[Attribute(Attribute::TARGET_FUNCTION)]
class JsonProperty
{
    public function __construct(
        private string $name,
    )
    {
    }
}


