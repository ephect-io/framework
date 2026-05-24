<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class WhileParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        if(!str_starts_with(trim($parameter), '@while')) {
            $this->result = $parameter;
            return;
        }

        $re = '/@while +(.+) +do/m';
        $subst = '<% while($1) { %>';
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }
}
