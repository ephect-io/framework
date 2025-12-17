<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class IfParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $re = '/@if +([\w \|@%&!=\'"+\*\/;\<\-\>\(\)\[\]]+) +do/m';
        $subst = "<% if ($1) {%>";
        $result = preg_replace($re, $subst, $parameter);

        //        if(strpos($result,'<% <%if') > -1) {
        //            $result = str_replace('<%if', 'if', $result);
        //        }
        //
        //        if(strpos($result,'%> <%') > -1) {
        //            $result = str_replace('%> <%', '', $result);
        //        }

        $this->result = $result;
    }
}
