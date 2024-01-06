<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\CLI\Console;
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

        $re = '/%([\w]+)/m';
        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $useVar = $match[1];
            $this->useVariables[$useVar] = '$' . $useVar;

            $text = str_replace($match[0], '$' . $useVar, $text);
        }

        $this->result = $text;

    }

}