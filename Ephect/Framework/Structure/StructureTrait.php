<?php

namespace Ephect\Framework\Structure;

use Error;

trait StructureTrait
{
    protected StructureInterface $structure;

    protected function bindStructure(StructureInterface $structure)
    {
        $this->structure = $structure;
        foreach ($structure as $key => $value) {
            if (!property_exists($this, $key)) {
                $class = get_class($this);
                throw new Error("The property [$key] is not defined in {$class}.");
            }

            $this->{$key} = $value;
        }
    }

    public function getStructure(): StructureInterface
    {
        return $this->structure;
    }
}
