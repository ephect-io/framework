<?php

namespace Ephect\Framework\Components\Generators;

interface ParserInterface
{
    public function getHtml(): ?string;
    public function doCache(): bool;
    public function doUncache(): bool;
    public function doArguments(string $componentArgs): ?array;
    public static function doArgumentsToString(array $componentArgs): ?string;
}
