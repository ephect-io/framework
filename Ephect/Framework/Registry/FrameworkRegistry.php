<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\ElementUtils;
use Ephect\Framework\Utils\File;

class FrameworkRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function reset(): void
    {
        $runtimeDir = applicationRuntimePath();

        self::$instance = new FrameworkRegistry;
        self::$instance->_setCacheDirectory($runtimeDir);
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractStaticRegistry
    {
        if (self::$instance === null) {
            $runtimeDir = applicationRuntimePath();

            self::$instance = new FrameworkRegistry;
            self::$instance->_setCacheDirectory($runtimeDir);
        }

        return self::$instance;
    }

    public static function register(): void
    {
        if (!FrameworkRegistry::load(true)) {

            $frameworkFiles = File::walkTreeFiltered(EPHECT_ROOT, ['php']);

            foreach ($frameworkFiles as $filename) {
                if (
                    $filename === 'bootstrap.php'
                    || str_contains($filename, 'constants.php')
                    || str_contains($filename, 'Autoloader.php')
                ) {
                    continue;
                }

                if (str_ends_with($filename, 'Interface.php')) {
                    [$namespace, $interface] = ElementUtils::getInterfaceDefinitionFromFile(EPHECT_ROOT . $filename);
                    $fqname = $namespace . '\\' . $interface;
                    FrameworkRegistry::write($fqname, EPHECT_ROOT . $filename);
                    continue;
                }

                if(str_ends_with($filename, 'Enum.php')) {
                    [$namespace, $enum] = ElementUtils::getEnumDefinitionFromFile(EPHECT_ROOT . $filename);
                    $fqname = $namespace . '\\' . $enum;
                    FrameworkRegistry::write($fqname, EPHECT_ROOT . $filename);
                    continue;
                }

                if (str_ends_with($filename, 'Trait.php')) {
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

            self::registerCustomClasses(SRC_ROOT);

            FrameworkRegistry::save(true);

        }
    }

    public static function registerCustomClasses(string $customDir): void
    {
        if (!file_exists($customDir)) return;
        $collectedClasses = self::collectCustomClasses($customDir);

        foreach ($collectedClasses as $class => $filename) {
            FrameworkRegistry::write($class, $filename);
        }
    }

    public static function collectCustomClasses(string $customDir): array
    {
//        if (!file_exists($customDir)) return [];

        $result = [];

        $sourceFiles = File::walkTreeFiltered($customDir, ['php']);

        foreach ($sourceFiles as $filename) {
            if (str_ends_with($filename, 'Interface.php')) {
                [$namespace, $interface] = ElementUtils::getInterfaceDefinitionFromFile($customDir . $filename);
                $fqname = $namespace . '\\' . $interface;
                $result[$fqname] = $customDir . $filename;
                continue;
            }

            if (str_ends_with($filename, 'Trait.php')) {
                [$namespace, $trait] = ElementUtils::getTraitDefinitionFromFile($customDir . $filename);
                $fqname = $namespace . '\\' . $trait;
                $result[$fqname] = $customDir . $filename;
                continue;
            }

            if(str_ends_with($filename, 'Enum.php')) {
                [$namespace, $enum] = ElementUtils::getEnumDefinitionFromFile($customDir . $filename);
                $fqname = $namespace . '\\' . $enum;
                $result[$fqname] = $customDir . $filename;
                continue;
            }

            [$namespace, $class] = ElementUtils::getClassDefinitionFromFile($customDir . $filename);
            $fqname = $namespace . '\\' . $class;
            if ($class === '') {
                [$namespace, $function] = ElementUtils::getFunctionDefinitionFromFile($customDir . $filename);
                $fqname = $namespace . '\\' . $function;
            }
            if ($fqname !== '\\') {
                $result[$fqname] = $customDir . $filename;
            }
        }

        return $result;
    }

    public static function registerModulesClasses(): void
    {
        [$filename, $paths] = PluginRegistry::readPluginPaths();
        foreach ($paths as $path) {
            self::registerCustomClasses($path);
        }

    }
}
