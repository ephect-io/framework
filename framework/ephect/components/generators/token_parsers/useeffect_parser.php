<?php

namespace Ephect\Components\Generators\TokenParsers;

final class UseEffectParser extends AbstractTokenParser
{
    public function do(null|string|array $parameter = null): void
    {
        $re = '/useEffect\(function[ ]+\(\)[ ]+use[ ]+\(((\s|.*?)+)\)[ ]+{/m';

        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        $match = count($matches) === 0 ?: !isset($matches[0][1]) ?: $matches[0][1];
        if ($match === true) {
            $this->result = '';
            return;
        }

        $useVars = explode(',', $match);
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

        $decl2 = implode(' ', $declVars);

        $decl1 = substr($this->html, 0, $this->component->getBodyStart() + 1);
        $decl3 = substr($this->html, $this->component->getBodyStart() + 1);

        $this->html = $decl1 . "\n\t" . $decl2 . "\n" . $decl3;
    }
}
