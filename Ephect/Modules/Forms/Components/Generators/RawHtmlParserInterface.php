<?php

namespace Ephect\Modules\Forms\Components\Generators;

interface RawHtmlParserInterface
{
    public function doTag(string $tag): void;

    public function getInnerHtml(): array;

    public function getOuterHtml(): array;
}
