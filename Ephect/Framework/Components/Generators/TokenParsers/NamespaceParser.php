<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class NamespaceParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/namespace([ ]+)(\w+)([ ]+)?;([ ]+)?/';
        $subst = 'namespace \\2;';

        $str = $this->html;

        $this->html = preg_replace($re, $subst, $str);
    }

}