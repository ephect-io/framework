<?php

namespace FunCom\Components;

use FunCom\ElementInterface;

interface ComponentInterface extends ElementInterface
{
    public function getParentHTML(): ?string;
    public function getCode();
    public function getFullyQualifiedFunction(): string;
    public function getFunction(): ?string;
}
