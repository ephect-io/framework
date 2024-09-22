<?php

namespace Forms\Generators\TokenParsers\View;

use Forms\Generators\TokenParsers\AbstractTokenParser;

final class EndParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@done/m';
        $subst = '<% } %>';
        $result = preg_replace($re, $subst, $parameter, 1);

        $this->result = $result;
    }

}
