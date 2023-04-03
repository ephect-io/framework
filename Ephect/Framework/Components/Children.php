<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\Tree\Tree;

class Children extends Tree implements ChildrenInterface
{
    use ElementTrait;

    protected ?object $props = null;
    protected ?array $parentProps = null;
    protected ?object $buffer = null;

    protected ?string $name = null;

    public function __construct(ChildrenStructure $struct)
    {
        $this->uid = $struct->uid;
        $this->motherUID = $struct->motherUID;
        $this->props = $struct->props;
        $this->parentProps = $struct->parentProps;
        $this->name = $struct->name;
        $this->class = $struct->class;
        $this->buffer = $struct->buffer;
    }

    function getName(): string
    {
        return $this->name;
    }

    public function props(): array|object
    {
        return $this->props;
    }

    public function getBuffer(): string
    {
        $fn = $this->buffer;

        ob_start();
        $fn();
        return ob_get_clean();
    }

    public function render(): void
    {
        $fn = $this->buffer;

        $fn();
    }

}
