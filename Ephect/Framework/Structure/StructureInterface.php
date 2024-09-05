<?php

namespace Ephect\Framework\Structure;

interface StructureInterface
{
    public function toArray(): array;

    public function encode(int $jsonOptions = JSON_PRETTY_PRINT): string;

    public function decode(string|array $input): void;
}
