<?php

use Ephect\Framework\Core\Autoloader;
use Ephect\Framework\Modules\ModuleInstaller;
use Ephect\Framework\Plugins\PluginInstaller;
use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Registry\HooksRegistry;

//define('DIRECTORY_SEPARATOR', Phar::running() ? '_' : DIRECTORY_SEPARATOR);
define('FRAMEWORK_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Framework' . DIRECTORY_SEPARATOR);
define('HOOKS_DIR', 'Hooks' . DIRECTORY_SEPARATOR);

include  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.php';

include  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants_utils.php';

include FRAMEWORK_PATH . 'Core' . DIRECTORY_SEPARATOR . 'Autoloader.php';
include FRAMEWORK_PATH . 'Utils' . DIRECTORY_SEPARATOR . 'File.php';
include FRAMEWORK_PATH . 'Utils' . DIRECTORY_SEPARATOR . 'Text.php';
include FRAMEWORK_PATH . 'ElementTrait.php';
include FRAMEWORK_PATH . 'ElementUtils.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'RegistryInterface.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'StaticRegistryInterface.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'AbstractRegistry.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'StaticRegistryTrait.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'AbstractStaticRegistry.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'FrameworkRegistry.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'HooksRegistry.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'AbstractStateRegistry.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'StateRegistry.php';
include FRAMEWORK_PATH . 'Registry' . DIRECTORY_SEPARATOR . 'MemoryRegistry.php';
include FRAMEWORK_PATH . 'Modules' . DIRECTORY_SEPARATOR . 'ModuleInstaller.php';
include FRAMEWORK_PATH . 'Plugins' . DIRECTORY_SEPARATOR . 'PluginInstaller.php';

HooksRegistry::register();
FrameworkRegistry::register();

Autoloader::register();

ModuleInstaller::loadBootstraps();
PluginInstaller::loadBootstraps();
