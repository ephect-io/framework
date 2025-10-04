<?php

namespace Ephect\Framework\Utils;

use ReflectionException;
use ReflectionFunction;
use SplFileObject;

class Text
{
    public static function slugify(string $text): ?string
    {
        // replace non letter or digits by -
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text)) {
            return null;
        }

        $text = iconv('us-ascii', 'utf-8', $text);

        return $text;
    }

    public static function format(string|array|object $string, ...$params): string
    {
        $result = $string;

        if (is_object($string)) {
            $result = json_encode($string, JSON_PRETTY_PRINT);
        }
        if (is_array($string)) {
            $result = print_r($string, true);
        }
        if (count($params) > 0 && count($params[0]) > 0) {
            $result = vsprintf($string, $params[0]);
        }

        return $result;
    }

    public static function jsonToPhpReturnedArray(string|array $json, bool $prettify = true): string
    {
        $array = [];
        if (!is_array($json)) {
            $array = json_decode($json, JSON_OBJECT_AS_ARRAY);
        }
        $result = '<?php' . PHP_EOL;
        $result .= 'return ';

        $result .= self::arrayToString($array, $prettify);
        $result .= ';' . PHP_EOL;

        return $result;
    }

    public static function arrayToString(array $array, bool $prettify = false): string
    {
        $dump = var_export($array, true);

        $convert = '';

        $re = '/(.*)(\'(.*)\' =>)( +)?(\n)( +)/m';
        $subst = "$1$2";
        $entries = preg_replace($re, $subst, $dump);
        $buffer = $entries;

        $entryRx = '/( +)?((.*) =>)?(((array) \()| \'?((.|\s)*?)\'?,$)?(\n)?/m';
        $closeArrayRx = '/^( +)?\),?(\n)?/';

        $isSpinning = false;
        $countSpinning = 0;
        $isDirty = false;

        try {
            while (strlen($buffer) > 0 && !$isSpinning) {
                $isDirty = false;

                if (preg_match($closeArrayRx, $buffer, $matches)) {
                    $indent = !isset($matches[1]) ? '' : $matches[1];
                    $convert .= $indent . ']' . ($indent == '' ? '' : ',');
                    $convert .= "\n";
                    $stringLen = strlen($matches[0]);
                    $buffer = substr($buffer, $stringLen);
                    $isDirty = true;
                } elseif (preg_match($entryRx, $buffer, $matches)) {
                    $indent = !isset($matches[1]) ? '' : $matches[1];
                    $convert .= $indent;
                    $key = !isset($matches[3]) ? '' : $matches[3];

                    if (isset($matches[6]) && $matches[6] == 'array') {
                        $convert .= !empty($key) ? $key . ' => [' : '[';

                        $stringLen = strlen($matches[0]);
                        $buffer = substr($buffer, $stringLen);
                        $isDirty = true;
                    } else {
                        $value = !isset($matches[4]) ? '' : $matches[4];

                        if (isset($matches[7]) && str_starts_with($matches[7], 'function')) {
                            $value = stripslashes($matches[7]) . ',';
                        }

                        if ($key !== '' && $key[0] == "'") {
                            $convert .= $key . ' => ' . $value;
                        } else {
                            $convert .= $value;
                        }

                        $stringLen = strlen($matches[0]);
                        $buffer = substr($buffer, $stringLen);
                        $isDirty = true;
                    }
                    $convert .= "\n";

                }

                if (!$isDirty) {
                    $countSpinning++;
                }

                $isSpinning = $countSpinning > 10;
            }

        } catch (\Exception $exception) {
            throw new Exception("Something went wrong while converting array to string", 1, $exception);
        }

        if (!$prettify) {
            $convert = str_replace("\n", "", $convert);
        }

        return $convert;
    }

    /**
     * @throws ReflectionException
     */
    private function callableToString(callable $controller): string
    {
        $ref = new ReflectionFunction($controller);

        $file = new SplFileObject($ref->getFileName());
        $file->seek($ref->getStartLine() - 1);

        $code = '';
        while ($file->key() < $ref->getEndLine()) {
            $code .= $file->current();
            $file->next();
        }

        $begin = strpos($code, 'function');
        $end = strrpos($code, '}');
        $code = substr($code, $begin, $end - $begin + 1);

        return $code;
    }

}
