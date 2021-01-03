<?php

namespace FunCom;

use FunCom\IO\Utils;

final class ElementUtils 
{
    
    public static function getFunctionDefinition($contents): ?array
    {
        $namespace = self::grabKeywordName('namespace', $contents, ';');
        $functionName = self::grabKeywordName("\nfunction", $contents, '(');

        return [$namespace, $functionName];
    }

    public static function getTraitDefinition($contents): ?array
    {
        $namespace = self::grabKeywordName('namespace', $contents, ';');
        $traitName = self::grabKeywordName('trait', $contents, ' ');
        $traitName = trim($traitName, '{');
        $traitName = trim($traitName);
        
        return [$namespace, $traitName];
    }

    public static function getInterfaceDefinition($contents): ?array
    {
        $namespace = self::grabKeywordName('namespace', $contents, ';');
        $interfaceName = self::grabKeywordName('interface', $contents, ' ');
        $interfaceName = trim($interfaceName, '{');
        $interfaceName = trim($interfaceName);
        
        return [$namespace, $interfaceName];
    }

    public static function getClassDefinition($contents): ?array
    {
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

    public static function getNamespaceFromFQClassName($fqClassName): string
    {
        $typeParts = explode('\\', $fqClassName);
        $type = array_pop($typeParts);
        $namespace = implode('\\', $typeParts);

        return $namespace;
    }

    public static function getFunctionDefinitionFromFile($filepath): ?array
    {
        $contents = Utils::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getFunctionDefinition($contents);
    }

    public static function getTraitDefinitionFromFile($filepath): ?array
    {
        $contents = Utils::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getTraitDefinition($contents);
    }

    public static function getInterfaceDefinitionFromFile($filepath): ?array
    {
        $contents = Utils::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getInterfaceDefinition($contents);
    }

    public static function getClassDefinitionFromFile($filepath): ?array
    {
        $contents = Utils::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getClassDefinition($contents);
    }
}