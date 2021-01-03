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

        $props  = (new PropsValidator($this->props, $this->fields))->validate();
        $values = array_values($props);
        
        $result = new $type(...$values);

        return $result;
    }
    
}