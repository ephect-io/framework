<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers;

final class ArgumentsParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $componentArgs = $parameter;

        $re = '/([A-Za-z0-9_-]+)(\[\])?=(\"([\S ][^"]*)\"|\'([\S]*)\'|\{\{ ([\w]*) \}\}|\{([\S ]*)\})/m';

        preg_match_all($re, $componentArgs, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = substr(substr($match[3], 1), 0, -1);

            if (isset($match[2]) && $match[2] === '[]') {
                if (!isset($result[$key])) {
                    $result[$key] = [];
                }
                $result[$key][] = $value;
            } else {
                $result[$key] = $value;
            }
        }
    }

}