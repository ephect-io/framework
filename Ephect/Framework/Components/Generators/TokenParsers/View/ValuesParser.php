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

        $re = '/(%|@)([\w;]+)/m';
        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $variable = $match[2];
            $entity = html_entity_decode($match[0]);

            if ($variable === '' || $match[0] !== $entity) {
                continue;
            }

            $useVar = $variable;
            if ($match[1] !== '@') {
                $this->useVariables[$useVar] = '$' . $useVar;
            }

            $text = str_replace($match[0], '$' . $useVar, $text);
        }

        $this->result = $text;

    }

}