<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

abstract class AbstractComponentParser extends AbstractTokenParser
{

    abstract public function do(object|array|string|null $parameter = null): void;

    public static function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $pair = '"' . $key . '" => ' . (urlencode($value) != $value ?  'urldecode("' . urlencode($value) . '"), ' : '"' . $value . '", ');
            if ($value[0] === '$') {
                $pair = '"' . $key . '" => ' . $value . ', ';
            }
            $result .= $pair;
        }
        return ($result === '') ? null : '[' . $result . ']';
    }
}