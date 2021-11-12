<?php

namespace Ephect\CLI;

use Ephect\Element;
use Ephect\ElementTrait;
use Ephect\Utils\TextUtils;

class Console extends Element
{
    use ElementTrait;

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

    
    public static function readLine(?string $prompt = null): string
    {
        $result = '';

        $result = readline($prompt);
        readline_add_history($result);

        return $result;
    }

    public static function writeException(\Throwable $ex, $file = null, $line = null): void
    {
        if (!IS_WEB_APP) {
            $message = '';

            if ($ex instanceof \ErrorException) {
                $message .= 'Error severity: ' . $ex->getSeverity() . PHP_EOL;
            }
            $message .= 'Error code: ' . $ex->getCode() . PHP_EOL;
            $message .= 'In ' . $ex->getFile() . ', line ' . $ex->getLine() . PHP_EOL;
            $message .= 'With the message: ' . $ex->getMessage() . PHP_EOL;
            $message .= 'Stack trace: ' . $ex->getTraceAsString() . PHP_EOL;

            print "\033[41m\033[1;37m" . $message . "\033[0m\033[0m";
        } else {
            self::getLogger()->error($ex, $file, $line);
        }
    }
}
