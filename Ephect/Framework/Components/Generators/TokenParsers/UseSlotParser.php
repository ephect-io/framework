<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Components\Generators\TypesParserTrait;
use JetBrains\PhpStorm\Deprecated;

/**
 * Deprecated
 */
#[Deprecated("it does not work", "useEffect", "0.3")]
final class UseSlotParser extends AbstractTokenParser
{
    use TypesParserTrait;

    private string $text = '';

    public function do(null|string|array $parameter = null): void
    {
        if (!strpos($this->html, 'useSlot')) {
            return;
        }

        $this->doTranslation($parameter);
        $this->doDeclaration($parameter);
    }

    private function doTranslation(null|string|array $parameter = null): void
    {
        $re = '/useSlot\(function[ ]*\(((\$props|\$children),[ ]*)?((\s|.*?)+)\)[ ]+(use[ ]*\(((\s|.*?)+)\)[ ]*)?{((\s|.*?)+)}\);/m';

        $str = $this->html;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $params = count($matches) === 0 ?: !isset($matches[0][3]) ?: $matches[0][3];
        $uses = count($matches) === 0 ?: !isset($matches[0][6]) ?: $matches[0][6];

        if ($params === true) {
            $this->result = '';
            return;
        } elseif ($params !== '') {
            $this->text = $matches[0][0];
            $params = str_replace('$', '&$', $params) . ', ';
        }

        $this->html = preg_replace($re, 'useSlot(function() use ($1' . $params . $uses . ') {$8});', $this->html, 1);

    }

    private function doDeclaration(null|string|array $parameter = null): void
    {
        $re = '/useSlot\(function[ ]*\(\)[ ]+use[ ]*\(((\s|.*?)+)\)[ ]*{((\s|.*?)+)}\);/m';

        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $match1 = count($matches) === 0 ?: !isset($matches[0][1]) ?: $matches[0][1];
        $match2 = count($matches) === 0 ?: !isset($matches[0][3]) ?: $matches[0][3];
        if ($match1 === true) {
            $this->result = '';
            return;
        }

        // $text = $matches[0][0];

        $useVars = explode(',', $match1);
        $declVars = array_filter($useVars, function ($item) {
            return $item !== '$props' && $item !== '$children' && trim($item) !== '';

        });

        $declVars = count($declVars) === 0 ?: array_map(function ($item) {
            return $this->declareTypedVariables($item);
        }, $declVars);

        if ($declVars === true) {
            $this->result = '';
            return;
        }

        $this->useVariables = $declVars;

        //$decl2 = implode(' ', $declVars);
        $decl2 = "";

        $useEffect = <<< USEFFECT
        \Ephect\Hooks\useEffect(function() use ($match1) { $match2 });
        USEFFECT;

        $this->result = [
            $this->text,
            "\n\t" . $decl2 . "\n" . $useEffect,
        ];

    }
}
