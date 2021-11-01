<?php

namespace Ephect\Registry;

use Ephect\ElementUtils;
use Ephect\IO\Utils;

class FrameworkRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new FrameworkRegistry();
            self::$instance->_setCacheDirectory(RUNTIME_DIR);
        }

        return self::$instance;
    }

    public static function register(): void
    {
        if (!FrameworkRegistry::uncache()) {

            $frameworkFiles = Utils::walkTreeFiltered(FRAMEWORK_ROOT, ['php']);

            foreach ($frameworkFiles as $filename) {
                if (
                    $filename === 'bootstrap.php'
                    || false !== strpos($filename, 'constants.php')
                    || false !== strpos($filename, 'autoloader.php')
                ) {
                    continue;
                }

                if (false !== strpos($filename, 'interface')) {
                    list($namespace, $interface) = ElementUtils::getInterfaceDefinitionFromFile(FRAMEWORK_ROOT . $filename);
                    $fqname = $namespace . '\\' . $interface;
                    FrameworkRegistry::write($fqname, FRAMEWORK_ROOT . $filename);
                    continue;
                }

                if (false !== strpos($filename, 'trait')) {
                    list($namespace, $trait) = ElementUtils::getTraitDefinitionFromFile(FRAMEWORK_ROOT . $filename);
                    $fqname = $namespace . '\\' . $trait;
                    FrameworkRegistry::write($fqname, FRAMEWORK_ROOT . $filename);
                    continue;
                }

                list($namespace, $class) = ElementUtils::getClassDefinitionFromFile(FRAMEWORK_ROOT . $filename);
                $fqname = $namespace . '\\' . $class;
                if ($class === '') {
                    list($namespace, $function) = ElementUtils::getFunctionDefinitionFromFile(FRAMEWORK_ROOT . $filename);
                    $fqname = $namespace . '\\' . $function;
                }
                if ($fqname !== '\\') {
                    FrameworkRegistry::write($fqname, FRAMEWORK_ROOT . $filename);
                }
            }

            self::registerUserClasses();

            FrameworkRegistry::cache();
        }
    }

    public static function registerUserClasses(): void
    {
        $sourceFiles = Utils::walkTreeFiltered(SRC_ROOT, ['php']);

        foreach ($sourceFiles as $filename) {
            if (false !== strpos($filename, 'interface')) {
                list($namespace, $interface) = ElementUtils::getInterfaceDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $interface;
                FrameworkRegistry::write($fqname, SRC_ROOT . $filename);
                continue;
            }

            if (false !== strpos($filename, 'trait')) {
                list($namespace, $trait) = ElementUtils::getTraitDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $trait;
                FrameworkRegistry::write($fqname, SRC_ROOT . $filename);
                continue;
            }

            list($namespace, $class) = ElementUtils::getClassDefinitionFromFile(SRC_ROOT . $filename);
            $fqname = $namespace . '\\' . $class;
            if ($class === '') {
                list($namespace, $function) = ElementUtils::getFunctionDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $function;
            }
            if ($fqname !== '\\') {
                FrameworkRegistry::write($fqname, SRC_ROOT . $filename);
            }
        }
    }
}
