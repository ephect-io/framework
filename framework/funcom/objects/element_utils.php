<?php

namespace FunCom;

use FunCom\IO\Utils;

final class ElementUtils 
{
    
    public static function getFunctionDefinition($filepath): ?array
    {
        $contents = Utils::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        $namespace = self::grabKeywordName('namespace', $contents, ';');
        $functionName = self::grabKeywordName('function', $contents, '(');

        return [$namespace, $functionName];
    }

    public static function getTraitDefinition($filepath): ?array
    {
        $contents = Utils::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        $namespace = self::grabKeywordName('namespace', $contents, ';');
        $traitName = self::grabKeywordName('trait', $contents, ' ');
        $traitName = trim($traitName, '{');
        $traitName = trim($traitName);
        
        return [$namespace, $traitName];
    }

    public static function getInterfaceDefinition($filepath): ?array
    {
        $contents = Utils::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        $namespace = self::grabKeywordName('namespace', $contents, ';');
        $interfaceName = self::grabKeywordName('interface', $contents, ' ');
        $interfaceName = trim($interfaceName, '{');
        $interfaceName = trim($interfaceName);
        
        return [$namespace, $interfaceName];
    }

    public static function getClassDefinition($filepath): ?array
    {
        $contents = Utils::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        $namespace = self::grabKeywordName('namespace', $contents, ';');
        $className = self::grabKeywordName('class', $contents, ' ');
        $className = trim($className, '{');
        $className = trim($className);

        return [$namespace, $className];
    }

    public static function grabKeywordName(string $keyword, string $classText, string $delimiter): string
    {
        $result = '';

        $start = strpos($classText, $keyword);
        if ($start > -1) {
            $start += \strlen($keyword) + 1;
            $end = strpos($classText, $delimiter, $start);
            $result = trim(substr($classText, $start, $end - $start));
        }

        return $result;
    }
}