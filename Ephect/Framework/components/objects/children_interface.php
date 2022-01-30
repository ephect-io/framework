<?php

namespace Ephect\Framework\Components;

use Ephect\Framework\ElementInterface;
use Ephect\Framework\Tree\TreeInterface;

interface ChildrenInterface extends TreeInterface, ElementInterface
{
    function props(): array|object;
    function onrender(): void;
}