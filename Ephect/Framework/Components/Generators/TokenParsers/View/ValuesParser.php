<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class ValuesParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {

        $text = '';
        if($parameter !== null && is_array($parameter)) {
            $text = $parameter['html'];
            $this->useVariables = $parameter['useVariables'];
        }

        $re = '/(&|@)([\w]+)/m';
        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $variable = $match[2];

            if ($variable === '') {
                continue;
            }

            $useVar = $variable;

            if ($match[1] !== '@') {
                $this->useVariables[$useVar] = '$' . $useVar;
            }

        }

        $subst = '\$$2';
        $this->result = preg_replace($re, $subst, $text);

    }

}