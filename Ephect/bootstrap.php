<?php

use Ephect\Framework\IO\Utils;

define('LIBDIR_SEPARATOR', Phar::running() ? '_' : DIRECTORY_SEPARATOR);
define('FRAMEWORK_PATH',  dirname(__FILE__) . LIBDIR_SEPARATOR . 'Framework' . LIBDIR_SEPARATOR);
define('HOOKS_PATH', 'Hooks' . LIBDIR_SEPARATOR);
define('PLUGINS_PATH', 'Plugins' . LIBDIR_SEPARATOR);

include FRAMEWORK_PATH . 'Core' . LIBDIR_SEPARATOR . 'constants.php';
include FRAMEWORK_PATH . 'Core' . LIBDIR_SEPARATOR . 'Autoloader.php';
include FRAMEWORK_PATH . 'Io' . LIBDIR_SEPARATOR . 'Utils.php';
include FRAMEWORK_PATH . 'ElementTrait.php';
include FRAMEWORK_PATH . 'ElementUtils.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'AbstractRegistryInterface.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'StaticRegistryInterface.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'AbstractRegistry.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'AbstractStaticRegistry.php';
include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'FrameworkRegistry.php';

if (!IS_PHAR_APP) {

    $hooks = [];
    $dir_handle = opendir(EPHECT_ROOT . HOOKS_PATH);

    while (false !== $filename = readdir($dir_handle)) {
        if ($filename == '.' || $filename == '..') {
            continue;
        }

        array_push($hooks, str_replace(DIRECTORY_SEPARATOR, '_' , HOOKS_PATH . $filename));

        include HOOKS_PATH . $filename;
    }

    $hooksRegistry = ['Hooks' => $hooks];

    Utils::safeWrite(RUNTIME_DIR . 'HooksRegistry.json',  json_encode($hooksRegistry));
}

if (IS_PHAR_APP) {
    $hooksRegistry = Utils::safeRead(RUNTIME_DIR . 'HooksRegistry.json');

    $hooks = json_decode($hooksRegistry);
    $hooks = $hooks->hooks;
    
    foreach($hooks as $hook) {
        include $hook;
    }
}

use Ephect\Framework\Registry\FrameworkRegistry;
use Ephect\Framework\Core\Autoloader;
use Ephect\Framework\Registry\PharRegistry;

FrameworkRegistry::register();
if(IS_PHAR_APP) {
    include FRAMEWORK_PATH . 'Registry' . LIBDIR_SEPARATOR . 'PharRegistry.php';
    PharRegistry::register();
}

Autoloader::register();
