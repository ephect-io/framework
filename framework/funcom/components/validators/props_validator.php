<?php

namespace FunCom\Components\Validators;

use InvalidArgumentException;

class PropsValidator
{
    public function validate(object $props, array $proptNames): ?array
    {
        $result = [];

        foreach($proptNames as $argument) {

            if(!isset($props->$argument)) {
                $result = null;
                throw new InvalidArgumentException("Argument $argument is missing.");
            }
    
            $result[$argument] = $props->$argument;
        }

        return $result;
    }
}