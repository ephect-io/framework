<?php

namespace Ephect\Framework\Structure;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class Inject
{
    public function __construct(
        private string $class = '',
        private array $params = [],
        private bool $singleton = false,
        private bool $fromContainer = false,
    ) {
    }
}
