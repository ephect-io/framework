<?php

namespace Ephect\Framework\Structure;

use Error;

trait StructureTrait
{
    protected function bindStructure(StructureInterface $structure)
    {

        foreach ($structure as $key => $value) {
            if (!property_exists($this, $key)) {
                $class = get_class($this);
                throw new Error("The property [$key] is not defined in {$class}.");
            }

            $this->{$key} = $value;
        }
    }
}