<?php

namespace FunCom\Components;

interface ComponentInterface
{
    public function getParentHTML(): ?string;
    public function getCode();
    public function getFullyQualifiedFunction(): string;
    public function getNamespace(): ?string;
    public function getFunction(): ?string;
}
