<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class ForParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/(\<for *?\(([\w @&!=\'"+;\<\-\>]+)\) *?\{)/m';
        $subst = '<? $1 ?>';
        $result = preg_replace($re, $subst, $parameter);

        if(strpos($result,'<? <for') > -1) {
            $result = str_replace('<for', 'for', $result);
        }

        $this->result = $result;
    }

}