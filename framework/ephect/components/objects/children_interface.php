<?php

namespace Ephect\Components;

use Ephect\ElementInterface;
use Ephect\Tree\TreeInterface;

interface ChildrenInterface extends TreeInterface, ElementInterface
{
    function props(): array;
    function onrender(): void;
}