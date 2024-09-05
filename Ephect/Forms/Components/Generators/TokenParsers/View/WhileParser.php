<?php

namespace Ephect\Forms\Components\Generators\TokenParsers\View;

use Ephect\Forms\Components\Generators\TokenParsers\AbstractTokenParser;

final class WhileParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@while ([\w @%&!=\'"+\*\/;\<\-\>\(\)\[\]]+) do/m';
        $subst = '<% while($1) { %>';
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }

}
