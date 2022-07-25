<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Components\Generators\TypesParserTrait;

final class UseEffectParser extends AbstractTokenParser
{

    use TypesParserTrait;

    public function do(null|string|array $parameter = null): void
    {
        if(!strpos($this->html, 'useEffect')) {
            return;
        }
        
        $this->doTranslation($parameter);
        $this->doDeclaration($parameter);
    }

    private function doTranslation(null|string|array $parameter = null): void
    {
        $re = '/useEffect\(function[ ]*\(((\$props|\$children),[ ]*)?((\s|.*?)+)\)[ ]+(use[ ]*\(((\s|.*?)+)\)[ ]*)?{((\s|.*?)+)}\);/m';
            
        $str = $this->html;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
     
        $props = count($matches) === 0 ?: !isset($matches[0][2]) ?: $matches[0][2];
        $params = count($matches) === 0 ?: !isset($matches[0][3]) ?: $matches[0][3];
        $uses = count($matches) === 0 ?: !isset($matches[0][6]) ?: $matches[0][6];

        if($props !== '') {
            $props .= ', ';
        }

        $byref = '';
        if ($params === true) {
            $this->result = '';
            return;
        } elseif ($params !== '') {
            $byref = str_replace('$', '&$', $params) . ', ';
        }

        $use = '';
        if($uses !== '') {
            $use = " use ($uses) ";
        }

        $this->html = preg_replace($re, 'useEffect(function($1' . $byref . ')' . $use . ' {$8}, ' . $props . $params . ');', $this->html, 1);

    }

    private function doDeclaration(null|string|array $parameter = null): void
    {
        $re = '/useEffect\(function[ ]*\(\)[ ]+use[ ]*\(((\s|.*?)+)\)[ ]*{/m';
        $re = '/useEffect\(function[ ]*\(((\s|.*?)+)\)[ ]*{/m';

        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $match = count($matches) === 0 ?: !isset($matches[0][1]) ?: $matches[0][1];
        if ($match === true) {
            $this->result = '';
            return;
        }

        $useVars = explode(',', $match);
        $declVars = array_filter($useVars, function ($item) {
            return $item !== '$props' && $item !== '$children' && trim($item) !== '';
        });

        $declVars = count($declVars) === 0 ?: array_map(function($item) {
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
