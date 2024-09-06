<?php

namespace Ephect\Modules\WebApp\Builder\Parsers;

interface ParserTypeInterface
{
    public function parse(): array;
}