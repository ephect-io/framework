<?php

namespace Ephect\Components\Generators\TokenParsers;

final class UseSlotParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/useSlot\(function ?\(\) use \(((\s|.*?)+)\) {((\s|.*?)+)}\);/m';

        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $match1 = count($matches) === 0 ?: !isset($matches[0][1]) ?: $matches[0][1];
        $match2 = count($matches) === 0 ?: !isset($matches[0][3]) ?: $matches[0][3];
        if ($match1 === true) {
            $this->result = '';
            return;
        }

        $text = $matches[0][0];

        $useVars = explode(',', $match1);
        $declVars = array_filter($useVars, function ($item) {
            return $item !== '$props' && $item !== '$children';
        });

        $declVars = count($declVars) === 0 ?: array_map(function ($item) {
            $item = trim($item);
            $item = str_replace('&', '', $item);

            $isset = false;
            if (strpos($item, '* bool *') > -1) {
                $isset = true;
                return $item . ' = false; ';
            }
            if (strpos($item, '* int *') > -1) {
                $isset = true;
                return $item . ' = 0; ';
            }
            if (strpos($item, '* float *') > -1) {
                $isset = true;
                return $item . ' = 0.0; ';
            }
            if (strpos($item, '* real *') > -1) {
                $isset = true;
                return $item . ' = 0.0; ';
            }
            if (strpos($item, '* string *') > -1) {
                $isset = true;
                return $item . ' = \'\'; ';
            }
            if (strpos($item, '* array *') > -1) {
                $isset = true;
                return $item . ' = []; ';
            }
            if (!$isset) {
                return $item . ' = null; ';
            }
        }, $declVars);

        if ($declVars === true) {
            $this->result = '';
            return;            
        }

        $this->useVariables = $declVars;

        //$decl2 = implode(' ', $declVars);
        $decl2 = "";

        $useEffect = <<< USEFFECT
        \Ephect\Hooks\useEffect(function () use ($match1) { $match2 });
        USEFFECT;

        $this->result = [
            $text,
            "\n\t" . $decl2 . "\n" . $useEffect,
        ];

    }
}
