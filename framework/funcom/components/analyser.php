<?php

namespace FunCom\Components;

class Analyser 
{


    public static function getFunctionDefinition(string $filename): array
    {
        $classText = file_get_contents($filename);

        if($classText === false) {
            return [null, null, false];
        }

        $namespace = self::grabKeywordName('namespace', $classText, ';');
        $functionName = self::grabKeywordName('function', $classText, '(');

        return [$namespace, $functionName, $classText];
    }

    public static function getClassDefinition(string $filename): array
    {
        $classText = file_get_contents($filename);

        $namespace = self::grabKeywordName('namespace', $classText, ';');
        $className = self::grabKeywordName('class', $classText, ' ');

        return [$namespace, $className, $classText];
    }

    public static function grabKeywordName(string $keyword, string $classText, $delimiter): string
    {
        $result = '';

        $start = strpos($classText, $keyword);
        if ($start > -1) {
            $start += \strlen($keyword) + 1;
            $end = strpos($classText, $delimiter, $start);
            $result = substr($classText, $start, $end - $start);
        }

        return $result;
    }
}