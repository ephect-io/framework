<?php

include 'funcom/core/autoloader.php';
include 'funcom/core/constants.php';
include FUNCOM_ROOT . 'io' . DIRECTORY_SEPARATOR . 'utils.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'static_registry_interface.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'abstract_registry.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'abstract_static_registry.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR . 'framework_registry.php';

use FunCom\ElementUtils;
use FunCom\IO\Utils;
use FunCom\Registry\FrameworkRegistry;
use FunCom\Core\Autoloader;

if (!FrameworkRegistry::uncache()) {

    include FUNCOM_ROOT . 'objects' . DIRECTORY_SEPARATOR . 'element_utils.php';

    $frameworkFiles = Utils::walkTreeFiltered(FRAMEWORK_ROOT, ['php']);
    array_shift($frameworkFiles);
    foreach ($frameworkFiles as $filename) {
        if (false !== strpos($filename, 'trait')) {
            list($namespace, $trait) = ElementUtils::getTraitDefinitionFromFile(FRAMEWORK_ROOT . $filename);
            $fqname = $namespace . '\\' . $trait;
            if ($fqname !=='\\') {
                FrameworkRegistry::write($fqname, $filename);
            }
            continue;
        }

        if (false !== strpos($filename, 'interface')) {
            list($namespace, $interface) = ElementUtils::getInterfaceDefinitionFromFile(FRAMEWORK_ROOT . $filename);
            $fqname = $namespace . '\\' . $interface;
            if ($fqname !=='\\') {
                FrameworkRegistry::write($fqname, $filename);
            }
            continue;
        }

        list($namespace, $class) = ElementUtils::getClassDefinitionFromFile(FRAMEWORK_ROOT . $filename);
        $fqname = $namespace . '\\' . $class;
        if ($fqname !=='\\') {
            FrameworkRegistry::write($fqname, $filename);
        }
    }

    FrameworkRegistry::cache();
}

Autoloader::register();
