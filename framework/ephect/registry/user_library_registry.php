<?php

namespace Ephect\Registry;

use Ephect\ElementUtils;
use Ephect\IO\Utils;

class UserLibraryRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new UserLibraryRegistry();
            self::$instance->_setCacheDirectory(RUNTIME_DIR);
        }

        return self::$instance;
    }

    public static function register(): void
    {
        if (!UserLibraryRegistry::uncache()) {

            include EPHECT_ROOT . 'objects' . DIRECTORY_SEPARATOR . 'element_utils.php';
        
            $sourceFiles = Utils::walkTreeFiltered(SRC_ROOT, ['php']);
        
            foreach ($sourceFiles as $filename) {
                if (
                    $filename === 'bootstrap.php'
                    || false !== strpos($filename, 'constants.php')
                    || false !== strpos($filename, 'autoloader.php')
                ) {
                    continue;
                }
        
                if (false !== strpos($filename, 'interface')) {
                    list($namespace, $interface) = ElementUtils::getInterfaceDefinitionFromFile(SRC_ROOT . $filename);
                    $fqname = $namespace . '\\' . $interface;
                    UserLibraryRegistry::write($fqname, $filename);
                    continue;
                }
        
                if (false !== strpos($filename, 'trait')) {
                    list($namespace, $trait) = ElementUtils::getTraitDefinitionFromFile(SRC_ROOT . $filename);
                    $fqname = $namespace . '\\' . $trait;
                    UserLibraryRegistry::write($fqname, $filename);
                    continue;
                }
        
                list($namespace, $class) = ElementUtils::getClassDefinitionFromFile(SRC_ROOT . $filename);
                $fqname = $namespace . '\\' . $class;
                if ($class === '') {
                    list($namespace, $function) = ElementUtils::getFunctionDefinitionFromFile(SRC_ROOT . $filename);
                    $fqname = $namespace . '\\' . $function;
                }
                UserLibraryRegistry::write($fqname, $filename);
            }
        
            UserLibraryRegistry::cache();
        }
        
    }
}
