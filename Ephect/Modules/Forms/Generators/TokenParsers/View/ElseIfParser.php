<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class ElseIfParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@else *if +(([\w @%&!=\'"+\*\/;\<\-\>\(\)\[\]]+)) +do/m';
        $subst = '<%} elseif ($1) {%>';
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }

}
