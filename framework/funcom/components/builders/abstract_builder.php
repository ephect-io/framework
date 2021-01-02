<?php

namespace FunCom\Components\Builders;

use FunCom\Components\Validators\PropsValidator;
use FunCom\ElementInterface;

abstract class AbstractBuilder
{
    protected $props;
    protected $fields;

    public function __construct(object $props, array $fields)
    {
        $this->props = $props;
        $this->fields = $fields;
    }

    protected function buildEx(string $type): ElementInterface
    {
        $result = null;
        $props  = (new PropsValidator())->validate($this->props, $this->fields);
        
        $values = array_values($props);
        $args = implode(', ', $values);
        $result = new $type(...$values);

        return $result;
    }
    
}