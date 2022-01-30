<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Components\Generators\ParserServiceInterface;

interface TokenParserInterface extends ParserServiceInterface
{
    public function doCache(): bool;
    public function doUncache(): bool;
    public function do(null|string|array $parameter = null): void;
}