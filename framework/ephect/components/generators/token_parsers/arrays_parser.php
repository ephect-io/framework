<?php

namespace Ephect\Components\Generators\TokenParsers;

final class ArraysParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/\{\{ \.\.\.([a-z0-9_\-\>]*) \}\}/m';
        $su = '<?php echo print_r($\1, true) ?>';
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