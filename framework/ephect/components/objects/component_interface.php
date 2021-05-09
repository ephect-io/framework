<?php

namespace Ephect\Components;

use Ephect\ElementInterface;

interface ComponentInterface extends ElementInterface
{
    public function getParentHTML(): ?string;
    public function getCode(): ?string;
    public function getFullyQualifiedFunction(): ?string;
    public function getFunction(): ?string;
    public function getDeclaration(): ?ComponentDeclaration;
    public function getEntity(): ?ComponentEntity;
    public function getBodyStart(): int;
    public function resetDeclaration(): void;
    // public function render(): void;
}
