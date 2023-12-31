<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class VariablesParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {

        if($parameter !== null && is_array($parameter)) {
            $this->useVariables = $parameter['useVariables'];
        }

        $html = $this->component->getCode();

        $re = '/function \w+ *?\([\$\w, \(\)\/*]*\)([: \w]+)?(.|\s)? *?\{/';
        preg_match_all($re, $html, $matches, PREG_OFFSET_CAPTURE, 0);

        Console::log($matches[0]);

        $match = $matches[0];
        $start = $match[0][1] + strlen($match[0][0]) + 1;

        $re = '/( *?return \(<<< ?HTML)/';
        preg_match_all($re, $html, $matches, PREG_OFFSET_CAPTURE, 0);

        Console::log($matches[0]);

        $match = $matches[0];
        $end = $match[0][1];

        $functionCode = substr($html, $start, $end - $start);

        Console::log($functionCode);

        $re = '/(\$\w+)([->]+\w+)?/m';
        preg_match_all($re, $functionCode, $matches, PREG_SET_ORDER, 0);

        $variables = [];
        foreach ($matches as $match) {
            $variables[] = $match[1];
//            if ($match[1] !== '@') {
//                $this->useVariables[$useVar] = '$' . $useVar;
//            }
        }


        $variables = $variables ? array_unique($variables) : [];
        Console::log($variables);

    }

}