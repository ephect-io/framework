<?php

namespace Ephect\Components;

use Ephect\ElementInterface;

interface ComponentInterface extends ElementInterface
{
    public function getParentHTML(): ?string;
    public function getCode();
    public function getFullyQualifiedFunction(): string;
    public function getFunction(): ?string;
}
