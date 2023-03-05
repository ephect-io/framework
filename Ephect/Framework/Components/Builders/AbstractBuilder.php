<?php

namespace Ephect\Framework\Components\Builders;

use Ephect\Framework\Components\Validators\PropsValidator;
use Ephect\Framework\ElementInterface;

abstract class AbstractBuilder
{
    protected object $props;
    protected string $struct;

    public function __construct(object $props, string $struct)
    {
        $this->props = $props;
        $this->struct = $struct;
    }

    protected function buildEx(string $class): ElementInterface
    {

        $struct  = (new PropsValidator($this->props, $this->struct))->validate();

        return new $class($struct);
    }
    
}