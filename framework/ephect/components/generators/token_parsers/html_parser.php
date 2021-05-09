<?php

namespace Ephect\Components\Generators\TokenParsers;

final class PhpTagsParser extends AbstractTokenParser
{
    public function do(): void
    {

        $subject = $this->html;

        $re = '/return \(<<<HTML((.|\s)+)HTML\);/m';
        preg_match_all($re, $subject, $matches, PREG_SET_ORDER, 0);

        $this->result = !isset($matches[0]) ? '' : $matches[0][1];
    }
    
}