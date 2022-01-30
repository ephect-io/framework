<?php

use Ephect\Framework\IO\Utils;

define('LIBDIR_SEPARATOR', Phar::running() ? '_' : DIRECTORY_SEPARATOR);
define('FRAMEWORK_PATH',  dirname(__FILE__) . LIBDIR_SEPARATOR . 'framework' . LIBDIR_SEPARATOR);
define('HOOKS_PATH', 'hooks' . LIBDIR_SEPARATOR);
define('PLUGINS_PATH', 'plugins' . LIBDIR_SEPARATOR);

include FRAMEWORK_PATH . 'core' . LIBDIR_SEPARATOR . 'constants.php';
include FRAMEWORK_PATH . 'core' . LIBDIR_SEPARATOR . 'autoloader.php';
include FRAMEWORK_PATH . 'io' . LIBDIR_SEPARATOR . 'utils.php';
include FRAMEWORK_PATH . 'objects' . LIBDIR_SEPARATOR . 'element_trait.php';
include FRAMEWORK_PATH . 'objects' . LIBDIR_SEPARATOR . 'element_utils.php';
include FRAMEWORK_PATH . 'registry' . LIBDIR_SEPARATOR  . 'objects' . LIBDIR_SEPARATOR . 'abstract_registry_interface.php';
include FRAMEWORK_PATH . 'registry' . LIBDIR_SEPARATOR  . 'objects' . LIBDIR_SEPARATOR . 'static_registry_interface.php';
include FRAMEWORK_PATH . 'registry' . LIBDIR_SEPARATOR  . 'objects' . LIBDIR_SEPARATOR . 'abstract_registry.php';
include FRAMEWORK_PATH . 'registry' . LIBDIR_SEPARATOR  . 'objects' . LIBDIR_SEPARATOR . 'abstract_static_registry.php';
include FRAMEWORK_PATH . 'registry' . LIBDIR_SEPARATOR . 'framework_registry.php';

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

    $hooks_registry = ['hooks' => $hooks];

    Utils::safeWrite(RUNTIME_DIR . 'hooks_registry.json',  json_encode($hooks_registry));
}

if (IS_PHAR_APP) {
    $hooks_registry = Utils::safeRead(RUNTIME_DIR . 'hooks_registry.json');

    $hooks = json_decode($hooks_registry);
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
    include FRAMEWORK_PATH . 'registry' . LIBDIR_SEPARATOR . 'phar_registry.php';
    PharRegistry::register();
}

Autoloader::register();
