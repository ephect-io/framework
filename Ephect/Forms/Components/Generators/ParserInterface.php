<?php

namespace Ephect\Forms\Components\Generators;

interface ParserInterface
{
    public static function doArgumentsToString(array $componentArgs): ?string;

    public function getHtml(): ?string;

    public function doCache(): bool;

    public function doUncache(): bool;

    public function doArguments(string $componentArgs): ?array;
}
