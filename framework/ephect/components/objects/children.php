<?php

namespace Ephect\Components;

use Ephect\ElementTrait;
use Ephect\Tree\Tree;

class Children extends Tree implements ChildrenInterface
{
    use ElementTrait;

    protected $props = [];
    protected $parentProps = [];
    protected $onrender = null;

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
