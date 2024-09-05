<?php

namespace Ephect\Framework\Components\Generators\TokenParsers\View;

use Ephect\Framework\Components\Generators\TokenParsers\AbstractTokenParser;

final class EchoParser extends AbstractTokenParser
{
    public function do(null|string|array|object $parameter = null): void
    {
        $text = '';
        if (is_array($parameter)) {
            $text = $parameter['html'];
            $this->useVariables = $parameter['useVariables'];
        }

        $re = '/\{ ([\w_@$\-\>]*) \}/m';

        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $variable = $match[1];

            $useVar = $variable;
            $arrowPos = strpos($variable, '->');
            if ($arrowPos > -1) {
                $useVar = substr($useVar, 0, $arrowPos);
            }

            if ($useVar[0] !== '@') {
                $this->useVariables[$useVar] = '$' . $useVar;
            }

            $translate = $variable;
            if ($translate[0] === '@') {
                $translate = substr($translate, 1);
            }

            if ($variable === 'children') {

                $text = str_replace('{{ children }}', '<?php $children->render(); ?>', $text);
                $text = str_replace('{ children }', '$children->getBuffer()', $text);

                continue;
            }

            $text = str_replace('{{ ' . $variable . ' }}', '<?=$' . $translate . ' ?>', $text);
            $text = str_replace('{ ' . $variable . ' }', 'echo $' . $translate . ';', $text);
        }

        $this->result = $text;
    }

}