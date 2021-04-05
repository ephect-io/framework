<?php

namespace Ephect\Core;

use Error;

class Structure implements StructureInterface
{

    public function __construct(?array $props)
    {
        if ($props === null) {
            return null;
        }
        foreach ($props as $key => $value) {
            if(!property_exists($this, $key)) {
                throw new Error("The property [$key] is not defined.");
            }

            $this->{$key} = $value;
        }
    }

    public function toArray() : array
    {
        $result = get_object_vars($this);

        return $result;
    }
}
