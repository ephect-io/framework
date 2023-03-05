<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\ElementTrait;
use Ephect\Framework\Tree\Tree;

class Children extends Tree implements ChildrenInterface
{
    use ElementTrait;

    protected ?object $props = null;
    protected array $parentProps = [];
    protected ?object $onrender = null;

    public function __construct(ChildrenStructure $struct)
    {
        $this->uid = $struct->uid;
        $this->motherUID = $struct->motherUID;
        $this->props = $struct->props;
        $this->parentProps = $struct->parentProps;
        $this->class = $struct->class;
        $this->onrender = $struct->onrender;
    }

    public function props(): array|object
    {
        return $this->props;
    }

    public function onrender(): void
    {
        $fn = $this->onrender;

        $fn();
    }
}
