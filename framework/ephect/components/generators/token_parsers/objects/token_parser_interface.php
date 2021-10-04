<?php

namespace Ephect\Components\Generators\TokenParsers;

interface TokenParserInterface
{
    public function getHtml(): string;
    public function getResult(): null|string|array|bool;
    public function getVariables(): ?array;
    public function getUses(): ?array;
    public function doCache(): bool;
    public function doUncache(): bool;
    public function do(null|string|array $parameter = null): void;
}