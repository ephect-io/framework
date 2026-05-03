<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class ValuesParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {

        $text = '';
        if (is_array($parameter)) {
            $text = $parameter['html'];
            $this->useVariables = $parameter['useVariables'];
        }

        $re = '/[%|$]([\w.]+)/m';
        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);

        usort($matches, function ($a, $b) {
            return strlen($b[0]) - strlen($a[0]);
        });

        foreach ($matches as $match) {
            $match1 = $match[1];
            $textVar = $match1;

            $parts = explode('.', $textVar);
            $useVar = $textVar;
            if (count($parts) > 1) {
                $textVar = implode('->', $parts);
                $useVar = $parts[0];
            }

            $this->useVariables[$useVar] = '$' . $useVar;

            $text = str_replace($match[0], '$' . $textVar, $text);
        }

        $this->result = $text;
    }
}
