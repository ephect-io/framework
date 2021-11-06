<?php

namespace Ephect\CLI;

use Ephect\Utils\TextUtils;

class Console
{
    public static function write($string, ...$params): void
    {
        if (IS_WEB_APP) {
            return;
        }

        $value = TextUtils::concat($string, $params);

        echo $value;
    }

    public static function writeLine($string, ...$params): void
    {
        if (IS_WEB_APP) {
            return;
        }
        
        $value = TextUtils::concat($string, $params);

        echo $value . PHP_EOL;
    }
}
