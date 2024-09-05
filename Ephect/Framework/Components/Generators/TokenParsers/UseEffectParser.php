<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Components\Generators\TypesParserTrait;

final class UseEffectParser extends AbstractTokenParser
{

    use TypesParserTrait;

    public function do(null|string|array|object $parameter = null): void
    {
        if (!strpos($this->html, 'useEffect')) {
            return;
        }

        $this->doTranslation($parameter);
        $this->doDeclaration($parameter);
    }

    private function doTranslation(null|string|array $parameter = null): void
    {
        $re = '/useEffect\(function[ ]*\(((\$props|\$children|\$slot),[ ]*)?((\s|.*?)+)\)[ ]+(use[ ]*\(((\s|.*?)+)\)[ ]*)?{((\s|.*?)+)}\);/m';

        $str = $this->html;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $params = count($matches) === 0 ?: !isset($matches[0][3]) ?: $matches[0][3];
        $uses = count($matches) === 0 ?: !isset($matches[0][6]) ?: $matches[0][6];

        if ($params === true) {
            $this->result = '';
            return;
        } elseif ($params !== '') {
            $params = str_replace('$', '&$', $params) . ', ';
        }

        $this->html = preg_replace($re, 'useEffect(function() use ($1' . $params . $uses . ') {$8}, $2);', $this->html, 1);

    }

    private function doDeclaration(null|string|array $parameter = null): void
    {
        $re = '/useEffect\(function[ ]*\(\)[ ]+use[ ]*\(((\s|.*?)+)\)[ ]*{/m';

        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $match = count($matches) === 0 ?: !isset($matches[0][1]) ?: $matches[0][1];
        if ($match === true) {
            $this->result = '';
            return;
        }

        $useVars = explode(',', $match);
        $declVars = array_filter($useVars, function ($item) {
            return !in_array(trim($item), ['$props', '$children', '$slot', '']);
        });

        $declVars = count($declVars) === 0 ?: array_map(function ($item) {
            return $this->declareTypedVariables($item);
        }, $declVars);

        if ($declVars === true) {
            $this->result = '';
            return;
        }

        $decl2 = implode(' ', $declVars);

        $decl1 = substr($this->html, 0, $this->component->getBodyStart() + 1);
        $decl3 = substr($this->html, $this->component->getBodyStart() + 1);

        $this->html = $decl1 . "\n\t" . $decl2 . "\n" . $decl3;
    }
}
