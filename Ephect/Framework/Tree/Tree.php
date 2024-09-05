<?php

namespace Ephect\Framework\Tree;

class Tree implements TreeInterface
{
    use TreeTrait;

    public function __construct(array $list)
    {
        $this->elementList = $list;
    }
}