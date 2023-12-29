<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class WhileParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/(\<while ?\(([\w @&!=\'"+;\<\-\>\(\)\[\]]+)\) +\{)/m';
        $subst = '<% $1 %>';
        $result = preg_replace($re, $subst, $parameter);

        if(strpos($result,'<% <while') > -1) {
            $result = str_replace('<while', 'while', $result);
        }
//
/*        if(strpos($result,'?> <?') > -1) {*/
/*            $result = str_replace('?> <?', '', $result);*/
//        }

        $this->result = $result;
    }

}