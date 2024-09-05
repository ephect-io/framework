<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class ModuleComponentParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $this->useVariables = $parameter;

        $re = '/function[\w ]+\((\$slot)\)/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $match = $matches[0] ?? null;
        if ($match && $match[1] === '$slot') {
            $this->useVariables['slot'] = '$slot';
        }
    }

}