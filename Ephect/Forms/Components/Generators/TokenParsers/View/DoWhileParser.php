<?php

namespace Ephect\Forms\Components\Generators\TokenParsers\View;

use Ephect\Forms\Components\Generators\TokenParsers\AbstractTokenParser;

final class DoWhileParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@while ([\w @%&!=\'"+\*\/;\<\-\>\(\)\[\]]+)/m';
        $subst = '<%} while($1); %>';
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }

}
