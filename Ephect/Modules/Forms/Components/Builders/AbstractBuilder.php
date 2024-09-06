<?php

namespace Ephect\Modules\Forms\Components\Builders;

use Ephect\Framework\ElementInterface;
use Ephect\Modules\Forms\Components\Validators\PropsValidator;
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