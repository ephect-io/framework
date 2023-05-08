<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

final class EchoParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $this->useVariables = $parameter;

        $re = '/\{ ([A-Za-z0-9_@\-\>]*) \}/m';
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

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

                $this->html = str_replace('{{ children }}', '<?php $children->render(); ?>', $this->html);
                $this->html = str_replace('{ children }', '$children->getBuffer()', $this->html);

                continue;
            }

            $this->html = str_replace('{{ ' . $variable . ' }}', '<?php echo $' . $translate . '; ?>', $this->html);
            $this->html = str_replace('{ ' . $variable . ' }', 'echo $' . $translate . '', $this->html);
        }
    }

}