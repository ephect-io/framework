<?php

namespace Ephect\Framework\Components\Generators\TokenParsers;

use Ephect\Framework\Utils\Text;

abstract class AbstractComponentParser extends AbstractTokenParser
{

    abstract public function do(object|array|string|null $parameter = null): void;

    public static function doArgumentsToString(array $componentArgs): ?string
    {
        $result = '';

        foreach ($componentArgs as $key => $value) {
            if (is_array($value)) {
                $arrayString = Text::arrayToString($value);
                $pair = '"' . $key . '" => ' . $arrayString . ', ';
            } else {
                $pair = '"' . $key . '" => ' . (addslashes($value) != $value ?  "'" . addslashes($value) . "', " : "'" . $value . "', ");
            }
            if ($value[0] === '$') {
                $pair = '"' . $key . '" => ' . $value . ', ';
            }
            $result .= $pair;
        }
        return ($result === '') ? null : '[' . $result . ']';
    }
}