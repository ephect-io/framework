<?php

namespace Ephect\Framework\Registry;

use Ephect\Framework\ElementUtils;
use Ephect\Framework\IO\Utils;

class FrameworkRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void { 
        self::$instance = new FrameworkRegistry;
        self::$instance->_setCacheDirectory(RUNTIME_DIR);
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
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

            $frameworkFiles = Utils::walkTreeFiltered(EPHECT_ROOT, ['php']);

            foreach ($frameworkFiles as $filename) {
                if (
                    $filename === 'bootstrap.php'
                    || false !== strpos($filename, 'constants.php')
                    || false !== strpos($filename, 'Autoloader.php')
                ) {
                    continue;
                }

                if (false !== strpos($filename, 'Interface')) {
                    list($namespace, $interface) = ElementUtils::getInterfaceDefinitionFromFile(EPHECT_ROOT . $filename);
                    $fqname = $namespace . '\\' . $interface;
                    FrameworkRegistry::write($fqname, EPHECT_ROOT . $filename);
                    continue;
                }

                if (false !== strpos($filename, 'Trait')) {
                    list($namespace, $trait) = ElementUtils::getTraitDefinitionFromFile(EPHECT_ROOT . $filename);
                    $fqname = $namespace . '\\' . $trait;
                    FrameworkRegistry::write($fqname, EPHECT_ROOT . $filename);
                    continue;
                }

                list($namespace, $class) = ElementUtils::getClassDefinitionFromFile(EPHECT_ROOT . $filename);
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
        if(!file_exists(SRC_ROOT)) return;
        
        $sourceFiles = Utils::walkTreeFiltered(SRC_ROOT, ['php']);

        foreach ($sourceFiles as $filename) {
            if (false !== strpos($filename, 'Interface')) {
                list($namespace, $interface) = ElementUtils::getInterfaceDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $interface;
                FrameworkRegistry::write($fqname, SRC_ROOT . $filename);
                continue;
            }

            if (false !== strpos($filename, 'Trait')) {
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
