<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class ArraysParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $this->useVariables = $parameter;

        $re = '/\{\{ \.\.\.([a-z0-9_\-\>]*) \}\}/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $variable = $match[1];

            $useVar = $variable;
            $arrowPos = strpos($variable, '->');
            if ($arrowPos > -1) {
                $useVar = substr($useVar, 0, $arrowPos);
            }

            $this->useVariables[$useVar] = '$' . $useVar;

            if ($variable === 'children') {
                continue;
            }

            $this->html = str_replace('{{ ...' . $variable . ' }}', '<?php echo print_r($' . $variable . ', true) ?>', $this->html);
        }
    }

}