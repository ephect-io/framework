<?php

namespace Ephect\Components;

use Ephect\ElementInterface;
use Ephect\Tree\TreeInterface;

interface CompositionInterface extends ElementInterface
{
    public function getClassName(): string;
    public function items(): ?TreeInterface;
    public function bindNodes(): void;
}