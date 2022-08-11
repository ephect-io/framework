<?php

namespace Ephect\Framework\Components\Generators;

trait TypesParserTrait
{
    public function declareTypedVariables($item): string
    {
        $item = trim($item);
        $item = str_replace('&', '', $item);
        $name = substr($item, strpos($item, '$'));

        $isset = false;
        if (strpos($item, '/* bool */') > -1) {
            $isset = true;
            return $name . ' = false; ';
        }
        if (strpos($item, '/* int */') > -1) {
            $isset = true;
            return $name . ' = 0; ';
        }
        if (strpos($item, '/* float */') > -1) {
            $isset = true;
            return $name . ' = 0.0; ';
        }
        if (strpos($item, '/* real */') > -1) {
            $isset = true;
            return $name . ' = 0.0; ';
        }
        if (strpos($item, '/* string */') > -1) {
            $isset = true;
            return $name . ' = \'\'; ';
        }
        if (strpos($item, '/* array */') > -1) {
            $isset = true;
            return $name . ' = []; ';
        }
        if (!$isset) {
            return $name . ' = null; ';
        }
    }

    public function getDefaultValue($item): string
    {
        $item = trim($item);

        $name = substr($item, strpos($item, '$') + 1);

        $isset = false;
        if (strpos($item, '/* bool */') > -1) {
            $isset = true;
            return "'$name' => false";
        }
        if (strpos($item, '/* int */') > -1) {
            $isset = true;
            return "'$name' => 0";
        }
        if (strpos($item, '/* float */') > -1) {
            $isset = true;
            return "'$name' => 0.0";
        }
        if (strpos($item, '/* real */') > -1) {
            $isset = true;
            return "'$name' => 0.0";
        }
        if (strpos($item, '/* string */') > -1) {
            $isset = true;
            return "'$name' => ''";
        }
        if (strpos($item, '/* array */') > -1) {
            $isset = true;
            return "'$name' => []";
        }
        if (!$isset) {
            return "'$name' => null";
        }
    }
}
