<?php

include 'ephect/core/constants.php';
include EPHECT_ROOT . 'core' . DIRECTORY_SEPARATOR . 'autoloader.php';
include EPHECT_ROOT . 'io' . DIRECTORY_SEPARATOR . 'utils.php';
include EPHECT_ROOT . 'objects' . DIRECTORY_SEPARATOR . 'element_trait.php';
include EPHECT_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'abstract_registry_interface.php';
include EPHECT_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'static_registry_interface.php';
include EPHECT_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'abstract_registry.php';
include EPHECT_ROOT . 'registry' . DIRECTORY_SEPARATOR  . 'objects' . DIRECTORY_SEPARATOR . 'abstract_static_registry.php';
include EPHECT_ROOT . 'registry' . DIRECTORY_SEPARATOR . 'framework_registry.php';
include EPHECT_ROOT . 'registry' . DIRECTORY_SEPARATOR . 'user_library_registry.php';

include HOOKS_ROOT . 'use_effect.php';
include HOOKS_ROOT . 'use_state.php';

use Ephect\Registry\FrameworkRegistry;
use Ephect\Core\Autoloader;
use Ephect\Registry\UserLibraryRegistry;

FrameworkRegistry::register();
UserLibraryRegistry::register();

Autoloader::register();
