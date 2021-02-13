<?php

namespace FunCom\Components;

use FunCom\ElementInterface;

interface CompositionInterface extends ElementInterface
{
    public function getClassName(): string;
    public function getComponents(): array;
}