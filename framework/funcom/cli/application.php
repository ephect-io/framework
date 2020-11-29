<?php

namespace FunCom\CLI;

define('SITE_ROOT', dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR);
define('SRC_ROOT', SITE_ROOT .  'src' . DIRECTORY_SEPARATOR);
define('REL_CACHE_DIR', 'cache' . DIRECTORY_SEPARATOR);
define('CACHE_DIR', SITE_ROOT . REL_CACHE_DIR);

use FunCom\Components\Compiler;
use FunCom\Core\Application as CoreApplication;

class Application extends CoreApplication
{
    public function __construct()
    {
    }
}
