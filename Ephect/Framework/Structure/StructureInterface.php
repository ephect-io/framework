<?php

namespace Ephect\Framework\Structure;

interface StructureInterface
{
    public function toArray(): array;

    public function encode(int $jsonOptions = JSON_PRETTY_PRINT, bool $asArray = false): array|string;

    public function decode(string|array $input): void;
}
