<?php

namespace Ephect\Components\Generators\TokenParsers;

use Ephect\Components\Generators\TypesParserTrait;

final class UsePropsParser extends AbstractTokenParser
{

    use TypesParserTrait;

    public function do(null|string|array $parameter = null): void
    {
        // $re = '/useProps\(function[ ]*\(\$(props|children), ((\s|.*?)+)\)[ ]*{/m';
        // $re = '/useProps\(function[ ]*\((\$(props|children),[ ]*)?((\s|.*?)+)\)[ ]+use[ ]*\(((\s|.*?)+)\)[ ]*{((\s|.*?)+)}\);/m';
        $re = '/useProps\(function[ ]*\(((\$props|\$children),[ ]*)?((\s|.*?)+)\)[ ]+(use[ ]*\(((\s|.*?)+)\)[ ]*)?{((\s|.*?)+)}\);/m';
            
        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $props = count($matches) === 0 ?: !isset($matches[0][1]) ?: $matches[0][1];
        $params = count($matches) === 0 ?: !isset($matches[0][3]) ?: $matches[0][3];
        $uses = count($matches) === 0 ?: !isset($matches[0][6]) ?: $matches[0][6];

        if ($params === true) {
            $this->result = '';
            return;
        }

        $paramVars = explode(',', $params);
        $useslVars = $uses !== '' ? explode(',', $uses) : [];
        $declVarTypes = array_filter($paramVars, function ($item) {
            return $item !== '$props' && $item !== '$children';
        });

        if(count($useslVars) > 0) {
            $declVarTypes = array_merge($declVarTypes, $useslVars);
        }

        $declVarValues = count($declVarTypes) === 0 ?: array_map(function($item) {
            return $this->declareTypedVariables($item);
        }, $declVarTypes);

        if ($declVarValues === true) {
            $this->result = '';
            return;            
        }

        $params = str_replace('$', '&$', $params);
        $uses = str_replace('$', '&$', $uses);

        // $declDefValues = count($declVarTypes) === 0 ?: array_map(function($item) {
        //     return $this->getDefaultValue($item);
        // }, $declVarTypes);

        /**
         * 
         * $this->html =  preg_replace($re, 'useProps(function() use ($1$3,$6) {$8}, [' . implode(', ', $declDefValues) . '], $2);', $this->html, 1);
         */
        $this->html = preg_replace($re, 'useProps(function() use ($1' . $params . ', ' . $uses . ') {$8}, $2);', $this->html, 1);
            
        $decl2 = implode(' ', $declVarValues);

        $decl1 = substr($this->html, 0, $this->component->getBodyStart() + 1);
        $decl3 = substr($this->html, $this->component->getBodyStart() + 1);

        $this->html = $decl1 . "\n\t" . $decl2 . "\n" . $decl3;
    }
}
