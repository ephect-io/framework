<?php

namespace Ephect\Framework;

use Ephect\Framework\Utils\File;

use function strlen;

final class ElementUtils
{
    public static function getNamespaceFromFQClassName($fqClassName): string
    {
        $classParts = explode('\\', $fqClassName);
        array_pop($classParts);
        return implode('\\', $classParts);
    }

    public static function getFunctionDefinitionFromFile($filepath): ?array
    {
        $contents = File::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getFunctionDefinition($contents);
    }

    public static function getFunctionDefinition($contents): ?array
    {

        $re = '/namespace *?([\w\\\\]+);[\w\W\\\\]*function *?([$\w]+) *?\(([\w\W]*)\)\W*:? *?(\w+)?\W*(\{)/U';

        preg_match($re, $contents, $matches, PREG_OFFSET_CAPTURE, 0);

        if (!count($matches)) {
            return ['', '', '', '', -1];
        }
        $namespace = $matches[1][0];
        $functionName = $matches[2][0];
        $parameters = $matches[3][0];
        $returnedType = $matches[4][0];
        $pos = $matches[5][1];

        return [$namespace, $functionName, $parameters, $returnedType, $pos];
    }

    public static function getEnumDefinitionFromFile($filepath): ?array
    {
        $contents = File::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getEnumDefinition($contents);
    }

    public static function getEnumDefinition($contents): ?array
    {
        [$namespace, $pos] = self::grabKeywordName('namespace', $contents, ';');
        [$enumName, $pos] = self::grabKeywordName('enum', $contents, ' ');
        $pos = strpos($contents, '{', $pos);

        $enumName = trim($enumName, '{');
        $enumName = trim($enumName);

        return [$namespace, $enumName, $pos];
    }

    public static function grabKeywordName(string $keyword, string $classText, string $delimiter): array
    {
        $result = '';
        $needle = $keyword . ' ';

        $end = -1;
        $start = strpos($classText, $needle);
        if ($start > -1) {
            $start += strlen($needle);
            $end = strpos($classText, $delimiter, $start);
            $result = trim(substr($classText, $start, $end - $start));
        }

        return [$result, $end];
    }

    public static function getTraitDefinitionFromFile($filepath): ?array
    {
        $contents = File::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getTraitDefinition($contents);
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

    public static function getInterfaceDefinitionFromFile($filepath): ?array
    {
        $contents = File::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getInterfaceDefinition($contents);
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

    public static function getClassDefinitionFromFile($filepath): ?array
    {
        $contents = File::safeRead($filepath);

        if ($contents === null) {
            return null;
        }

        return self::getClassDefinition($contents);
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

    public static function normalizeClassname(string $classname): string
    {
        $classNameParts = explode('\\', $classname);
        if (count($classNameParts) === 1) {
            $classname = \Constants::CONFIG_NAMESPACE . '\\' . $classname;
        }

        return $classname;
    }


}
