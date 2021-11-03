<?php

namespace Ephect\Components\Generators\TokenParsers;

final class ValuesParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $this->useVariables = $parameter;

        $re = '/\{([a-zA-Z0-9_@\-\>]*)\}/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $variable = $match[1];

            if($variable === '') {
                continue;
            }

            $useVar = $variable;
            $arrowPos = strpos($variable, '->');
            if ($arrowPos > -1) {
                $useVar = substr($useVar, 0, $arrowPos);
            }

            if ($variable[0] !== '@') {
                $this->useVariables[$useVar] = '$' . $useVar;
            }

            $translate = $variable;
            if ($translate[0] === '@') {
                $translate = substr($translate, 1);
            }

            $this->html = str_replace('{' . $variable . '}', '$' . $translate . '', $this->html);
        }

    }
    
}