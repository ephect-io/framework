<?php

$document_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '';
define('DOCUMENT_ROOT', $document_root);

define('SITE_ROOT', dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR);
define('FRAMEWORK_ROOT', SITE_ROOT .  'framework' . DIRECTORY_SEPARATOR);
define('FUNCOM_ROOT', FRAMEWORK_ROOT .  'funcom' . DIRECTORY_SEPARATOR);
define('SRC_ROOT', SITE_ROOT .  'src' . DIRECTORY_SEPARATOR);
define('REL_RUNTIME_DIR', 'runtime' . DIRECTORY_SEPARATOR);
define('RUNTIME_DIR', SITE_ROOT . REL_RUNTIME_DIR);
define('REL_CACHE_DIR', 'cache' . DIRECTORY_SEPARATOR);
define('CACHE_DIR', SITE_ROOT . REL_CACHE_DIR);
define('LOG_PATH', SRC_ROOT . 'logs' . DIRECTORY_SEPARATOR);
define('DEBUG_LOG', LOG_PATH . 'debug.log');
define('ERROR_LOG', LOG_PATH . 'error.log');
define('SQL_LOG', LOG_PATH . 'sql.log');
