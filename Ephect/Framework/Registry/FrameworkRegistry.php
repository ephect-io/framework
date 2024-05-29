<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\ElementUtils;
use Ephect\Framework\Utils\File;

class FrameworkRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        self::$instance = new FrameworkRegistry;
        self::$instance->_setCacheDirectory(RUNTIME_DIR);
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new FrameworkRegistry;
            self::$instance->_setCacheDirectory(RUNTIME_DIR);
        }

        return self::$instance;
    }

    public static function register(): void
    {
        if (!FrameworkRegistry::uncache(true)) {

            $frameworkFiles = File::walkTreeFiltered(EPHECT_ROOT, ['php']);

            foreach ($frameworkFiles as $filename) {
                if (
                    $filename === 'bootstrap.php'
                    || str_contains($filename, 'constants.php')
                    || str_contains($filename, 'Autoloader.php')
                ) {
                    continue;
                }

                if (str_contains($filename, 'Interface')) {
                    [$namespace, $interface] = ElementUtils::getInterfaceDefinitionFromFile(EPHECT_ROOT . $filename);
                    $fqname = $namespace . '\\' . $interface;
                    FrameworkRegistry::write($fqname, EPHECT_ROOT . $filename);
                    continue;
                }

                if(str_contains($filename, 'Enum')) {
                    [$namespace, $enum] = ElementUtils::getEnumDefinitionFromFile(EPHECT_ROOT . $filename);
                    $fqname = $namespace . '\\' . $enum;
                    FrameworkRegistry::write($fqname, EPHECT_ROOT . $filename);
                    continue;
                }

                if (str_contains($filename, 'Trait')) {
                    [$namespace, $trait] = ElementUtils::getTraitDefinitionFromFile(EPHECT_ROOT . $filename);
                    $fqname = $namespace . '\\' . $trait;
                    FrameworkRegistry::write($fqname, EPHECT_ROOT . $filename);
                    continue;
                }

                [$namespace, $class] = ElementUtils::getClassDefinitionFromFile(EPHECT_ROOT . $filename);
                $fqname = $namespace . '\\' . $class;
                if ($class === '') {
                    list($namespace, $function) = ElementUtils::getFunctionDefinitionFromFile(EPHECT_ROOT . $filename);
                    $fqname = $namespace . '\\' . $function;
                }
                if ($fqname !== '\\') {
                    FrameworkRegistry::write($fqname, EPHECT_ROOT . $filename);
                }
            }

            self::registerUserClasses();

            FrameworkRegistry::cache(true);

        }
    }

    public static function registerUserClasses(): void
    {
        if (!file_exists(SRC_ROOT)) return;

        $sourceFiles = File::walkTreeFiltered(SRC_ROOT, ['php']);

        foreach ($sourceFiles as $filename) {
            if (str_contains($filename, 'Interface')) {
                [$namespace, $interface] = ElementUtils::getInterfaceDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $interface;
                FrameworkRegistry::write($fqname, SRC_ROOT . $filename);
                continue;
            }

            if (str_contains($filename, 'Trait')) {
                [$namespace, $trait] = ElementUtils::getTraitDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $trait;
                FrameworkRegistry::write($fqname, SRC_ROOT . $filename);
                continue;
            }

            if(str_contains($filename, 'Enum')) {
                [$namespace, $enum] = ElementUtils::getEnumDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $enum;
                FrameworkRegistry::write($fqname, SRC_ROOT . $filename);
                continue;
            }

            [$namespace, $class] = ElementUtils::getClassDefinitionFromFile(SRC_ROOT . $filename);
            $fqname = $namespace . '\\' . $class;
            if ($class === '') {
                [$namespace, $function] = ElementUtils::getFunctionDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $function;
            }
            if ($fqname !== '\\') {
                FrameworkRegistry::write($fqname, SRC_ROOT . $filename);
            }
        }
    }
}
