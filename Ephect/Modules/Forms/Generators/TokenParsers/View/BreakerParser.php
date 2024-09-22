<?php

namespace Forms\Generators\TokenParsers\View;

use Forms\Generators\TokenParsers\AbstractTokenParser;

final class BreakerParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@(break|continue)/m';
        $subst = '<% $1; %>';
        $result = preg_replace($re, $subst, $parameter);

        /*        if(strpos($result,'?> <?') > -1) {*/
        /*            $result = str_replace('?> <?', '', $result);*/
//        }

        $this->result = $result;
    }

}
