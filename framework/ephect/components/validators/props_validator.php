<?php

namespace Ephect\Components\Validators;

use InvalidArgumentException;

class PropsValidator
{
    protected $props;
    protected $fields;

    public function __construct(object $props, array $fields)
    {
        $this->props = $props;
        $this->fields = $fields;
    }

    public function validate(): ?array
    {
        $result = [];

        foreach($this->fields as $field) {

            if(!isset($this->props->$field)) {
                $result = null;
                throw new InvalidArgumentException("Argument $field is missing.");
            }
    
            $result[$field] = $this->props->$field;
        }

        return $result;
    }
}