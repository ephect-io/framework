<?php

namespace Ephect\WebApp\Builder\Parsers;

interface ParserTypeInterface
{
    public function parse(): array;
}