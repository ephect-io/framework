<?php

namespace Ephect\Components\Builders;

use Ephect\Components\Validators\PropsValidator;
use Ephect\ElementInterface;

abstract class AbstractBuilder
{
    protected $props;
    protected $struct;

    public function __construct(object $props, string $struct)
    {
        $this->props = $props;
        $this->struct = $struct;
    }

    protected function buildEx(string $type): ElementInterface
    {
        $result = null;
        
        $struct  = (new PropsValidator($this->props, $this->struct))->validate();
        
        $result = new $type($struct);

        return $result;
    }
    
}