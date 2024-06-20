<?php

use Ephect\Framework\Core\Autoloader;
use Ephect\Framework\JavaScripts\AjilBuilder;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\HooksRegistry;
use Ephect\Framework\Registry\PharRegistry;

define('LIBDIR_SEPARATOR', Phar::running() ? '_' : DIRECTORY_SEPARATOR);
define('FRAMEWORK_PATH', dirname(__FILE__) . LIBDIR_SEPARATOR . 'Framework' . LIBDIR_SEPARATOR);
define('HOOKS_DIR', 'Hooks' . LIBDIR_SEPARATOR);

include FRAMEWORK_PATH . 'Core' . LIBDIR_SEPARATOR . 'constants.php';
include FRAMEWORK_PATH . 'Core' . LIBDIR_SEPARATOR . 'Autoloader.php';
include FRAMEWORK_PATH . 'Utils' . LIBDIR_SEPARATOR . 'File.php';
include FRAMEWORK_PATH . 'Utils' . LIBDIR_SEPARATOR . 'Text.php';
include FRAMEWORK_PATH . 'ElementTrait.php';
include FRAMEWORK_PATH . 'ElementUtils.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'RegistryInterface.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'StaticRegistryInterface.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'StateRegistryInterface.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'AbstractRegistry.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'StaticRegistryTrait.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'AbstractStaticRegistry.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'FrameworkRegistry.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'HooksRegistry.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'AbstractStateRegistry.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'StateRegistry.php';

HooksRegistry::register();

FrameworkRegistry::register();
if (IS_PHAR_APP) {
    include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'PharRegistry.php';
    PharRegistry::register();
}

Autoloader::register();

if (IS_WEB_APP) {
    AjilBuilder::build();
}
