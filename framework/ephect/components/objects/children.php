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
        $this->type = $struct->type;
        $this->onrender = $struct->onrender;
    }

    public function props(): array
    {
        $result = [];

        return $result;
    }

    public function onrender(): void
    {
        $fn = $this->onrender;

        $fn();
    }
}
