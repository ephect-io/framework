<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class AnyTagParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {

        $tag = $parameter;
        $subject = $this->html;

        $re = '/<\/?(' . $tag . ')(\s|.*?)?>/mu';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        $this->result = !isset($matches[0]) ? '' : $matches[0][1];
    }
    
}