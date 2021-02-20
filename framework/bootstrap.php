<?php

include 'ephect/core/constants.php';
include FUNCOM_ROOT . 'core' . DIRECTORY_SEPARATOR . 'autoloader.php';
include FUNCOM_ROOT . 'io' . DIRECTORY_SEPARATOR . 'utils.php';
include FUNCOM_ROOT . 'objects' . DIRECTORY_SEPARATOR . 'element_trait.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'abstract_registry_interface.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'static_registry_interface.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'abstract_registry.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'abstract_static_registry.php';
include FUNCOM_ROOT . 'registry' . DIRECTORY_SEPARATOR . 'framework_registry.php';

include HOOKS_ROOT . 'use_effect.php';
include HOOKS_ROOT . 'use_state.php';

use Ephect\ElementUtils;
use Ephect\IO\Utils;
use Ephect\Registry\FrameworkRegistry;
use Ephect\Core\Autoloader;

if (!FrameworkRegistry::uncache()) {

    include FUNCOM_ROOT . 'objects' . DIRECTORY_SEPARATOR . 'element_utils.php';

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
            FrameworkRegistry::write($fqname, $filename);
            continue;
        }

        if (false !== strpos($filename, 'trait')) {
            list($namespace, $trait) = ElementUtils::getTraitDefinitionFromFile(FRAMEWORK_ROOT . $filename);
            $fqname = $namespace . '\\' . $trait;
            FrameworkRegistry::write($fqname, $filename);
            continue;
        }

        list($namespace, $class) = ElementUtils::getClassDefinitionFromFile(FRAMEWORK_ROOT . $filename);
        $fqname = $namespace . '\\' . $class;
        if ($class === '') {
            list($namespace, $function) = ElementUtils::getFunctionDefinitionFromFile(FRAMEWORK_ROOT . $filename);
            $fqname = $namespace . '\\' . $function;
        }
        FrameworkRegistry::write($fqname, $filename);
    }

    FrameworkRegistry::cache();
}

Autoloader::register();
