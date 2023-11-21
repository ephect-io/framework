<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class IfParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/(\<if ?\(([\w @&!=\-\'"+;<>\(\)]+)\) +\{)/m';
        $subst = '<? $1 ?>';
        $result = preg_replace($re, $subst, $parameter);

        if(strpos($result,'<? <if') > -1) {
            $result = str_replace('<if', 'if', $result);
        }

        if(strpos($result,'?> <?') > -1) {
            $result = str_replace('?> <?', '', $result);
        }

        $this->result = $result;
    }

}