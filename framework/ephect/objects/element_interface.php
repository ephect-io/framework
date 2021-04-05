<?php

namespace Ephect;

interface ElementInterface
{
    public function getUID(): string;
    public function getMotherUID(): string;
    public function getId(): string;
    public function getParent(): ?ElementInterface;
    // public function getType(): string;
    // public function getFullType(): string;
    public function getBaseType(): string;
    public function getNamespace(): string;
}
