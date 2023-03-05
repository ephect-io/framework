<?php

namespace Ephect\Framework\Core;

use Error;

class Structure implements StructureInterface
{

    public function __construct($props)
    {
        if (!is_array($props) && !is_object($props)) {
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
        return get_object_vars($this);
    }
}
