<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class HeredocParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/(<<<[ ]*(HTML|html))/';

        $subst = '<<< \\2';

        $str = $this->html;

        $this->html = preg_replace($re, $subst, $str);       
    }
    
}