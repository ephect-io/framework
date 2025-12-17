<?php

namespace Ephect\Modules\Forms\Generators\TokenParsers\View;

use Ephect\Modules\Forms\Generators\TokenParsers\AbstractTokenParser;

final class VariablesParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {

        if (is_array($parameter)) {
            $this->useVariables = $parameter['useVariables'];
        }

        $html = $this->component->getCode();

        $re = '/function \w+ *?\([\$\w, ]*\):? *?\w*?(.|\s|\R)? *?\{/U';
        preg_match_all($re, $html, $matches, PREG_OFFSET_CAPTURE, 0);

        $match = $matches[0];
        $start = intval($match[0][1]) + strlen($match[0][0]) + 1;

        $re = '/\(([\$\w ,]*)\)/';
        preg_match_all($re, $html, $matches, PREG_OFFSET_CAPTURE, 0);

        $match = $matches[1];
        $funcArguments = explode(', ', $match[0][0]);

        $re = '/( *?return \(<<< ?HTML)/';
        preg_match_all($re, $html, $matches, PREG_OFFSET_CAPTURE, 0);

        $match = $matches[0];
        $end = $match[0][1];

        $functionCode = substr($html, $start, $end - $start);

        $re = '/(\$\w+)([->]+\w+)?/m';
        preg_match_all($re, $functionCode, $matches, PREG_SET_ORDER, 0);

        $variables = [];
        foreach ($matches as $match) {
            $variables[] = $match[1];
        }

        $variables = $variables ? array_unique($variables) : [];

        $useVariables = array_keys($this->useVariables);

        $c = count($useVariables);

        for ($i = $c - 1; $i > -1; $i--) {
            $current = '$' . $useVariables[$i];
            if (is_array($funcArguments) && in_array($current, $funcArguments)) {
                continue;
            }
            if (!in_array($current, $variables)) {
                unset($this->useVariables[$useVariables[$i]]);
            }
        }

        $this->funcVariables = $variables;
    }
}
