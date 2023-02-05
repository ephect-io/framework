<?php

namespace Ephect\Framework\CLI;

use Ephect\Framework\Element;
use Ephect\Framework\ElementTrait;
use Ephect\Framework\Utils\TextUtils;

class Console extends Element
{
    use ElementTrait;

    public static function write(string|array|object $string, ...$params): void
    {
        if (IS_WEB_APP) {
            return;
        }

        $value = TextUtils::format($string, $params);

        echo $value;
    }

    public static function writeLine(string|array|object $string, ...$params): void
    {
        if (IS_WEB_APP) {
            return;
        }
        
        $value = TextUtils::format($string, $params);

        echo $value . PHP_EOL;
    }
    
    public static function readLine(string $prompt): string
    {
        $result = '';

        $result = readline($prompt);
        readline_add_history($result);

        return $result;
    }

    public static function writeException(\Throwable $ex, $file = null, $line = null): void
    {
        if (!IS_WEB_APP) {
            $message =  self::formatException($ex);

            print "\033[41m\033[1;37m" . $message . "\033[0m\033[0m";
        } else {
            self::getLogger()->error($ex, $ex->getFile(), $ex->getLine());
        }
    }

    public static function formatException(\Throwable $ex): string
    {
        $message = '';

        if ($ex instanceof \ErrorException) {
            $message .= 'Error severity: ' . $ex->getSeverity() . PHP_EOL;
        }
        $message .= 'Error code: ' . $ex->getCode() . PHP_EOL;
        $message .= 'In ' . $ex->getFile() . ', line ' . $ex->getLine() . PHP_EOL;
        $message .= 'With the message: ' . $ex->getMessage() . PHP_EOL;
        $message .= 'Stack trace: ' . $ex->getTraceAsString() . PHP_EOL;

        return $message;
    }

    public static function log(string|array|object $string, ...$params): void 
    {
        if (IS_WEB_APP) {
            self::getLogger()->debug($string);
        }

        $value = TextUtils::format($string, $params);

        echo $value . PHP_EOL;
    }
}
