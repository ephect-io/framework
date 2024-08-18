<?php

namespace Ephect\Framework\Structure;

interface StructureInterface
{
    public function toArray(): array;

    public function encode(): string;

    public function decode(string|array $input): void;
}
