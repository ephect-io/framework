<?php

namespace Ephect\Forms\Components\Generators\TokenParsers\View;

use Ephect\Forms\Components\Generators\TokenParsers\AbstractTokenParser;

final class DoParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@do *$/m';
        $subst = '<% do {%>';
        $result = preg_replace($re, $subst, $parameter);

        $this->result = $result;
    }

}
