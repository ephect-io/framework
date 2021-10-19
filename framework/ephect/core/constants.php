<?php

$document_root = isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '';

$app_is_web = true;
if($document_root === '') {
    $document_root = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    $app_is_web = false;
}

$document_root = dirname(dirname($document_root)) . DIRECTORY_SEPARATOR;

define('IS_WEB_APP', $app_is_web);
define('IS_PHAR_APP', (\Phar::running() !== ''));
define('IS_CLI_APP', (\Phar::running() === '') && !IS_WEB_APP);

define('DOCUMENT_ROOT', $document_root);

define('SITE_ROOT', DOCUMENT_ROOT);
define('SRC_ROOT', SITE_ROOT .  'src' . DIRECTORY_SEPARATOR);
define('APP_DIR', 'app' . DIRECTORY_SEPARATOR);
define('APP_ROOT', SRC_ROOT . APP_DIR);
define('CONFIG_DIR', SRC_ROOT . 'config' . DIRECTORY_SEPARATOR);
define('REL_RUNTIME_DIR', 'runtime' . DIRECTORY_SEPARATOR);
define('RUNTIME_DIR', SITE_ROOT . REL_RUNTIME_DIR);
define('REL_CACHE_DIR', 'cache' . DIRECTORY_SEPARATOR);
define('CACHE_DIR', SITE_ROOT . REL_CACHE_DIR);
define('REL_STATIC_DIR', 'static' . DIRECTORY_SEPARATOR);
define('STATIC_DIR', CACHE_DIR . REL_STATIC_DIR);
define('REL_COPY_DIR', 'copy' . DIRECTORY_SEPARATOR);
define('COPY_DIR', CACHE_DIR . REL_COPY_DIR);
define('LOG_PATH', SITE_ROOT . 'logs' . DIRECTORY_SEPARATOR);
define('DEBUG_LOG', LOG_PATH . 'debug.log');
define('ERROR_LOG', LOG_PATH . 'error.log');
define('SQL_LOG', LOG_PATH . 'sql.log');
define('ROUTES_JSON', RUNTIME_DIR . 'routes.json');

if(IS_WEB_APP || IS_CLI_APP) {
    define('FRAMEWORK', trim(file_get_contents(CONFIG_DIR . 'framework')));
}

if(IS_PHAR_APP) {
    define('FRAMEWORK', 'framework');
}

define('FRAMEWORK_ROOT', SITE_ROOT .  FRAMEWORK . DIRECTORY_SEPARATOR);
define('EPHECT_ROOT', FRAMEWORK_ROOT . 'ephect' . DIRECTORY_SEPARATOR);
define('HOOKS_ROOT', FRAMEWORK_ROOT . 'hooks' . DIRECTORY_SEPARATOR);
define('PLUGINS_ROOT', FRAMEWORK_ROOT . 'plugins' . DIRECTORY_SEPARATOR);

$appname = pathinfo(SITE_ROOT, PATHINFO_FILENAME);
define('APP_NAME', $appname);
// define('EPHECT_VENDOR_SRC', 'vendor' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
// define('EPHECT_VENDOR_SRC', 'framework' . DIRECTORY_SEPARATOR);
define('EPHECT_VENDOR_SRC', FRAMEWORK_ROOT);
define('EPHECT_VENDOR_LIB', EPHECT_VENDOR_SRC . 'ephect' . DIRECTORY_SEPARATOR);
define('EPHECT_VENDOR_WIDGETS', EPHECT_VENDOR_SRC . 'widgets' . DIRECTORY_SEPARATOR);
define('EPHECT_VENDOR_PLUGINS', EPHECT_VENDOR_SRC . 'plugins' . DIRECTORY_SEPARATOR);
define('EPHECT_VENDOR_APPS', EPHECT_VENDOR_SRC . 'apps' . DIRECTORY_SEPARATOR);
define('EPHECTJS_VENDOR', EPHECT_VENDOR_SRC . 'phinkjs' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR);
// define('EPHECT_ROOT', SITE_ROOT . EPHECT_VENDOR_LIB);
define('EPHECT_WIDGETS_ROOT', SITE_ROOT . EPHECT_VENDOR_WIDGETS);
define('EPHECT_PLUGINS_ROOT', SITE_ROOT . EPHECT_VENDOR_PLUGINS);
define('EPHECT_APPS_ROOT', SITE_ROOT . EPHECT_VENDOR_APPS);
define('EPHECTJS_ROOT', SITE_ROOT . EPHECTJS_VENDOR);

define('CLASS_EXTENSION', '.class.php');
define('PREHTML_EXTENSION', '.phtml');
define('CSS_EXTENSION', '.css');
define('JS_EXTENSION', '.js');

if(!IS_WEB_APP) {
    define('REQUEST_URI', 'https://localhost/');
    define('REQUEST_METHOD', 'GET');
    define('QUERY_STRING', parse_url(REQUEST_URI, PHP_URL_QUERY));
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
define('HTTP_ORIGIN', isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '');
define('HTTP_ACCEPT', $_SERVER['HTTP_ACCEPT'] ?: '');
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

/**
 * TO BE TESTED ON SUBDIRECTORY
 */
// $hostPort = explode(':', HTTP_HOST);
// $is127 = (($host = array_shift($hostPort) . (isset($hostPort[1]) ? $port = ':' . $hostPort[1] : $port = '') == '127.0.0.1' . $port) ? $hostname = 'localhost' : $hostname = $host) !== $host;
// $isIndex = (((strpos(REQUEST_URI, 'index.php')  > -1) ? $requestUri = str_replace('index.php', '', REQUEST_URI) : $requestUri = REQUEST_URI) !== REQUEST_URI);

// if ($is127 || $isIndex) {
//     header('Location: //' . $hostname . $port . $requestUri);
//     exit(302);
// }
/**
 * END
 */