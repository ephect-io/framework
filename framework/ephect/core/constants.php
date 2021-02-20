<?php

$document_root = isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '';
define('DOCUMENT_ROOT', $document_root);

define('SITE_ROOT', dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR);
define('FRAMEWORK_ROOT', SITE_ROOT .  'framework' . DIRECTORY_SEPARATOR);
define('FUNCOM_ROOT', FRAMEWORK_ROOT .  'ephect' . DIRECTORY_SEPARATOR);
define('HOOKS_ROOT', FUNCOM_ROOT .  'hooks' . DIRECTORY_SEPARATOR);
define('PLUGINS_ROOT', FRAMEWORK_ROOT .  'plugins' . DIRECTORY_SEPARATOR);
define('SRC_ROOT', SITE_ROOT .  'src' . DIRECTORY_SEPARATOR);
define('APP_DIR', 'app' . DIRECTORY_SEPARATOR);
define('APP_ROOT', SRC_ROOT . APP_DIR);
define('CONFIG_DIR', SRC_ROOT . 'config' . DIRECTORY_SEPARATOR);
define('REL_RUNTIME_DIR', 'runtime' . DIRECTORY_SEPARATOR);
define('RUNTIME_DIR', SITE_ROOT . REL_RUNTIME_DIR);
define('REL_CACHE_DIR', 'cache' . DIRECTORY_SEPARATOR);
define('CACHE_DIR', SITE_ROOT . REL_CACHE_DIR);
define('LOG_PATH', SRC_ROOT . 'logs' . DIRECTORY_SEPARATOR);
define('DEBUG_LOG', LOG_PATH . 'debug.log');
define('ERROR_LOG', LOG_PATH . 'error.log');
define('SQL_LOG', LOG_PATH . 'sql.log');
define('ROUTES_JSON', RUNTIME_DIR . 'routes.json');

define('CLASS_EXTENSION', '.class.php');
define('PREHTML_EXTENSION', '.phtml');
define('CSS_EXTENSION', '.css');
define('JS_EXTENSION', '.js');

if(DOCUMENT_ROOT === '') {
    return;
}

$rewrite_base = '/';

if (file_exists(CONFIG_DIR . 'rewrite_base') && $rewrite_base = file_get_contents(CONFIG_DIR . 'rewrite_base')) {
    $rewrite_base = trim($rewrite_base);
}
define('REWRITE_BASE', $rewrite_base);


$scheme = 'http';
if (strstr($_SERVER['SERVER_SOFTWARE'], 'IIS')) {
    $scheme = ($_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
} elseif (strstr($_SERVER['SERVER_SOFTWARE'], 'Apache')) {
    $scheme = $_SERVER['REQUEST_SCHEME'];
} elseif (strstr($_SERVER['SERVER_SOFTWARE'], 'lighttpd')) {
    $scheme = strstr($_SERVER['SERVER_PROTOCOL'], 'HTPPS') ? 'https' : 'http';
} elseif (strstr($_SERVER['SERVER_SOFTWARE'], 'nginx')) {
    $scheme = strstr($_SERVER['SERVER_PROTOCOL'], 'HTPPS') ? 'https' : 'http';
}

define('HTTP_PROTOCOL', $scheme);
define('HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
define('HTTP_HOST', $_SERVER['HTTP_HOST']);
define('HTTP_ORIGIN', (isset($_SERVER['HTTP_ORIGIN'])) ? $_SERVER['HTTP_ORIGIN'] : '');
define('HTTP_ACCEPT', (isset($_SERVER['HTTP_ACCEPT'])) ? $_SERVER['HTTP_ACCEPT'] : '');
define('HTTP_PORT', $_SERVER['SERVER_PORT']);
define('COOKIE', $_COOKIE);
define('REQUEST_URI', $_SERVER['REQUEST_URI']);
define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
define('QUERY_STRING', parse_url(REQUEST_URI, PHP_URL_QUERY));
define('SERVER_NAME', $_SERVER['SERVER_NAME']);
define('SERVER_HOST', HTTP_PROTOCOL . '://' . HTTP_HOST);
define('SERVER_ROOT', HTTP_PROTOCOL . '://' . SERVER_NAME . ((HTTP_PORT !== '80' && HTTP_PORT !== '443') ? ':' . HTTP_PORT : ''));
define('BASE_URI', SERVER_NAME . ((HTTP_PORT !== '80') ? ':' . HTTP_PORT : '') . ((REQUEST_URI !== '') ? REQUEST_URI : ''));
define('FULL_URI', HTTP_PROTOCOL . '://' . BASE_URI);
define('FULL_SSL_URI', 'https://' . BASE_URI);
define('REL_RUNTIME_JS_DIR', 'js' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR);
define('REL_RUNTIME_CSS_DIR', 'css' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR);
define('RUNTIME_JS_DIR', DOCUMENT_ROOT . REL_RUNTIME_JS_DIR);
define('RUNTIME_CSS_DIR', DOCUMENT_ROOT . REL_RUNTIME_CSS_DIR);

