<?php

namespace Ephect\Components\Builders;

use Ephect\Components\Validators\PropsValidator;
use Ephect\ElementInterface;

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