<?php

namespace Ephect\Framework\Entity;

use Ephect\Framework\Core\StructureInterface;
use Ephect\Framework\ElementInterface;
use Ephect\Framework\ElementTrait;

class Entity implements ElementInterface
{

    use ElementTrait;

    public static function create(StructureInterface $struct): ElementInterface
    {
        return new self($struct);
    }
}