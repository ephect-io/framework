<?php

namespace FunCom\Components;

use FunCom\ElementInterface;
use FunCom\Tree\TreeInterface;

interface CompositionInterface extends ElementInterface
{
    public function getClassName(): string;
    public function items(): ?TreeInterface;
    public function bindNodes(): void;
}