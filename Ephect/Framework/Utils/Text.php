<?php

namespace Ephect\Framework\Utils;

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

    public static function jsonToPhpReturnedArray(string $json): string
    {
        $array = json_decode($json, JSON_OBJECT_AS_ARRAY);
        $result = '<?php' . PHP_EOL;
        $result .= 'return ';

        $result .= self::arrayToString($array);
        $result .= ';' . PHP_EOL;

        return $result;
    }

    public static function arrayToString(array $array): string
    {
        $dump = self::var_dump_r($array);

        $indentsLengths = [];
        $convert = '';

        $re = '/(.*)(\[[\w"-\/\\\\]+]=>)(\n)( +)/';
        $subst = "$1$2";
        $entries = preg_replace($re, $subst, $dump);
        $buffer = $entries;
        $offset = 0;

        $re = '/^( ?+)+/m';
        preg_match_all($re, $entries, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $indentsLengths[] = count($match) > 1 ? strlen($match[0]) : 0;
        }

        $entryRx = '/( ?+)+(\[([\w"-\/\\\\]+)]=>)?((array|string|int|float|bool)\(([\w.]+)\) ?(.*)\n)/';
        $closeArrayRx = '/^( ?+)+}/';

        $l = count($indentsLengths);
        for ($i = 0; $i < $l; $i++) {
            $indentLen = $indentsLengths[$i];
            $indent = $indentLen > 0 ? str_repeat(' ', $indentLen) : '';



            if (preg_match($closeArrayRx, $buffer, $matches)) {
                $convert .= $indent . ']' . ($indent == '' ? '' : ',');
                $convert .= "\n";
                $stringLen = strlen($matches[0]) + 1;
                $buffer = substr($buffer, $stringLen);
                $offset += $stringLen;
            } else if (preg_match($entryRx, $buffer, $matches)) {
                $convert .= $indent;
                if ($matches[5] == 'array') {
                    $convert .= !empty($matches[3]) ? "'" . trim($matches[3], '"') . "'" . ' => [' : '[';
                    $stringLen = strlen($matches[0]) + 1;
                    $buffer = substr($buffer, $stringLen);
                    $offset += $stringLen;
                } else if ($matches[5] == 'string') {
                    $len = intval($matches[6]);
                    $token = "string($len)";
                    $lenToken = strlen($token);
                    $posToken = strpos($matches[0], $token);
                    $start = $posToken + $lenToken + 2;

                    $value = substr($entries, $offset + $start, $len);
                    $quote = str_starts_with($value, 'function') ? '' : "'";
                    $value = $quote == '' ? $value : str_replace("\\", "\\\\", $value);

                    if ($j = substr_count($value, "\n")) {
                        $i += $j;
                    }
                    if (str_starts_with($matches[3], '"')) {
                        $key = "'" . trim($matches[3], '"') . "'";
                        $key = str_replace("\\", "\\\\", $key);
                        $convert .= $key . " => $quote" . $value . "$quote,";
                    } else {
                        $convert .= "$quote" . $value . "$quote,";
                    }

                    $stringLen = $start + $len + 2;
                    $buffer = substr($buffer, $stringLen);
                    $offset += $stringLen;
                } else {
                    if (str_starts_with($matches[3], '"')) {
                        $key = "'" . trim($matches[3], '"') . "'";
                        $key = str_replace("\\", "\\\\", $key);
                        $convert .= $key . " => " . $matches[6] . ",";
                    } else {
                        $convert .= $matches[6] . ',';
                    }
                    $stringLen = strlen($matches[0]) + 1;
                    $buffer = substr($buffer, $stringLen);
                    $offset += $stringLen;
                }
                $convert .= "\n";

            }
        }

        return $convert;
    }

    private static function var_dump_r(mixed $value): string
    {
        ob_start();
        var_dump($value);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
