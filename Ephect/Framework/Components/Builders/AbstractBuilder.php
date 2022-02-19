<?php

namespace Ephect\Framework\Components\Builders;

use Ephect\Framework\Components\Validators\PropsValidator;
use Ephect\Framework\ElementInterface;

abstract class AbstractBuilder
{
    protected $props;
    protected $struct;

    public function __construct(object $props, string $struct)
    {
        $this->props = $props;
        $this->struct = $struct;
    }

    protected function buildEx(string $class): ElementInterface
    {
        $result = null;
        
        $struct  = (new PropsValidator($this->props, $this->struct))->validate();
        
        $result = new $class($struct);

        return $result;
    }
    
}