<?php

namespace Ephect\Modules\Forms\Components\Generators\TokenParsers;

use Ephect\Modules\Forms\Components\Generators\ParserServiceInterface;

interface TokenParserInterface extends ParserServiceInterface
{
    public function doCache(): bool;

    public function doUncache(): bool;

    public function do(null|string|array|object $parameter = null): void;
}