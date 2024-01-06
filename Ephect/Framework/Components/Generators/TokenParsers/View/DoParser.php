<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class DoParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/(\<do *?\{|} *?while\(([\w @%&!=\'"+\*\/;\<\-\>\(\)\[\]]+)\)\>)/m';
        $subst = '<% $1 %>';
        $result = preg_replace($re, $subst, $parameter);

        if(strpos($result,'<% <do') > -1) {
            $result = str_replace('<do', 'do', $result);
        }
//
/*        if(strpos($result,')> ?>') > -1) {*/
//            $result = str_replace(')>', ')', $result);
//        }
//
/*        if(strpos($result,'?> <?') > -1) {*/
/*            $result = str_replace('?> <?', '', $result);*/
//        }

        $this->result = $result;
    }

}