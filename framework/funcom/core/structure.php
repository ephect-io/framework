<?php

namespace FunCom\Core;

use Error;

class Structure
{

    public function __construct(array $properties)
    {
        foreach ($properties as $key => $value) {
            if(!property_exists($this, $key)) {
                throw new Error("The property [$key] is not defined.");
            }

            $this->{$key} = $value;
        }
    }
}
