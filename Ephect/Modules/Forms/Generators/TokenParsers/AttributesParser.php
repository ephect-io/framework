<?php

namespace Forms\Generators\TokenParsers;

final class AttributesParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $this->result = [];

        $re = '/(#\[(\w+)\(.*\)])/m';
        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $this->result[] = $match[2];
        }
    }
}