<?php

namespace Ephect\Forms\Components\Builders;

use Ephect\Forms\Components\Validators\PropsValidator;
use Ephect\Framework\ElementInterface;
use ErrorException;

abstract class AbstractBuilder
{
    protected object $props;
    protected string $struct;

    public function __construct(object $props, string $struct)
    {
        $this->props = $props;
        $this->struct = $struct;
    }

    /**
     * @throws ErrorException
     */
    protected function buildEx(string $class): ElementInterface
    {
        $struct = (new PropsValidator($this->props, $this->struct))->validate();

        return new $class($struct);
    }

}