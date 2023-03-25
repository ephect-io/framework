<?php

namespace Ephect\Framework\Components\Generators;

interface RawHtmlParserInterface
{
    public function doTag(string $tag): void;
    public function getInnerHtml(): string;
}
