<?php

namespace Forms\Generators;

trait TypesParserTrait
{
    public function declareTypedVariables($item): string
    {
        $item = trim($item);
        $item = str_replace('&', '', $item);
        $name = substr($item, strpos($item, '$'));

        if (strpos($item, '/* bool */') > -1) {
            return $name . ' = false; ';
        }
        if (strpos($item, '/* int */') > -1) {
            return $name . ' = 0; ';
        }
        if (strpos($item, '/* float */') > -1) {
            return $name . ' = 0.0; ';
        }
        if (strpos($item, '/* real */') > -1) {
            return $name . ' = 0.0; ';
        }
        if (strpos($item, '/* string */') > -1) {
            return $name . ' = \'\'; ';
        }
        if (strpos($item, '/* array */') > -1) {
            return $name . ' = []; ';
        }

        return $name . ' = null; ';
    }

    public function getDefaultValue($item): string
    {
        $item = trim($item);

        $name = substr($item, strpos($item, '$') + 1);

        if (strpos($item, '/* bool */') > -1) {
            return "'$name' => false";
        }
        if (strpos($item, '/* int */') > -1) {
            return "'$name' => 0";
        }
        if (strpos($item, '/* float */') > -1) {
            return "'$name' => 0.0";
        }
        if (strpos($item, '/* real */') > -1) {
            return "'$name' => 0.0";
        }
        if (strpos($item, '/* string */') > -1) {
            return "'$name' => ''";
        }
        if (strpos($item, '/* array */') > -1) {
            return "'$name' => []";
        }

        return "'$name' => null";
    }
}
