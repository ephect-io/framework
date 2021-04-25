<?php

namespace Ephect;

use Ephect\IO\Utils;

final class ElementUtils 
{
    
    public static function getFunctionDefinition($contents): ?array
    {
        [$namespace, $pos] = self::grabKeywordName('namespace', $contents, ';');
        [$functionName, $pos] = self::grabKeywordName("\nfunction", $contents, '(');
        $pos = strpos($contents, '{', $pos);

        return [$namespace, $functionName, $pos];
    }

    public static function getTraitDefinition($contents): ?array
    {
        [$namespace, $pos] = self::grabKeywordName('namespace', $contents, ';');
        [$traitName, $pos] = self::grabKeywordName('trait', $contents, ' ');
        $pos = strpos($contents, '{', $pos);
        
        $traitName = trim($traitName, '{');
        $traitName = trim($traitName);
        
        return [$namespace, $traitName, $pos];
    }

    public static function getInterfaceDefinition($contents): ?array
    {
        [$namespace, $pos] = self::grabKeywordName('namespace', $contents, ';');
        [$interfaceName, $pos] = self::grabKeywordName('interface', $contents, ' ');
        $pos = strpos($contents, '{', $pos);
        
        $interfaceName = trim($interfaceName, '{');
        $interfaceName = trim($interfaceName);
        
        return [$namespace, $interfaceName, $pos];
    }

    public static function getClassDefinition($contents): ?array
    {
        [$namespace, $pos] = self::grabKeywordName('namespace', $contents, ';');
        [$className, $pos] = self::grabKeywordName('class', $contents, ' ');
        $pos = strpos($contents, '{', $pos);
        
        $className = trim($className, '{');
        $className = trim($className);

        return [$namespace, $className, $pos];
    }

    public static function grabKeywordName(string $keyword, string $classText, string $delimiter): array
    {
        $result = '';

        $end = -1;
        $start = strpos($classText, $keyword);
        if ($start > -1) {
            $start += \strlen($keyword) + 1;
            $end = strpos($classText, $delimiter, $start);
            $result = trim(substr($classText, $start, $end - $start));
        }

        return [$result, $end];
    }

    public static function getNamespaceFromFQClassName($fqClassName): string
    {
        $classParts = explode('\\', $fqClassName);
        $class = array_pop($classParts);
        $namespace = implode('\\', $classParts);

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