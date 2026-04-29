<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class BreakerParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        if(!str_starts_with(trim($parameter), '@break') && !str_starts_with(trim($parameter), '@continue')) {
            $this->result = $parameter;
            return;
        }
        $re = '/@(break|continue)/m';
        $subst = '<% $1; %>';
        $result = preg_replace($re, $subst, $parameter);

        /*        if(strpos($result,'?> <?') > -1) {*/
        /*            $result = str_replace('?> <?', '', $result);*/
        //        }

        $this->result = $result;
    }
}
