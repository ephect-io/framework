<?php
namespace Ephect\Utils;

class TextUtils
{
    // code derived from http://php.vrana.cz/vytvoreni-pratelskeho-url.php
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

    public static function concat($string, ...$params): string 
    {

        if(is_object($string)) {
            $string = json_encode($string, JSON_PRETTY_PRINT);
        }
        if (is_array($string)) {
            $string = print_r($string, true);
        }
        $result = $string;
        if (count($params) > 0 && is_array($params[0])) {
            $result = vsprintf($string, $params[0]);
            return $result;
        }
        if (count($params) > 0 && is_array($params)) {
            $result = vsprintf($string, $params);
            return $result;
        }
        
    }
}
