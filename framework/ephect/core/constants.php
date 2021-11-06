<?php

$document_root = isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '';

define('IS_WEB_APP', $document_root !== '');
define('IS_PHAR_APP', (\Phar::running() !== ''));
define('IS_CLI_APP', (\Phar::running() === '') && !IS_WEB_APP);

if (!IS_WEB_APP) {
    $document_root = (getcwd() ? getcwd() : __DIR__) . DIRECTORY_SEPARATOR;
}

define('DOCUMENT_ROOT', $document_root);

if (IS_WEB_APP) {

    $document_root = dirname(dirname($document_root)) . DIRECTORY_SEPARATOR;

    define('SITE_ROOT', $document_root);
    define('SRC_ROOT', SITE_ROOT . 'src' . DIRECTORY_SEPARATOR);

    define('CONFIG_DIR', SRC_ROOT . 'config' . DIRECTORY_SEPARATOR);

    define('FRAMEWORK', trim(file_get_contents(CONFIG_DIR . 'framework')));
    define('FRAMEWORK_ROOT', SITE_ROOT .  FRAMEWORK . DIRECTORY_SEPARATOR);

    $appname = pathinfo(SITE_ROOT, PATHINFO_FILENAME);
    define('APP_NAME', $appname);


    define('EPHECT_VENDOR_SRC', FRAMEWORK_ROOT);
    define('EPHECT_VENDOR_LIB', EPHECT_VENDOR_SRC . 'ephect' . DIRECTORY_SEPARATOR);
    define('EPHECT_VENDOR_APPS', EPHECT_VENDOR_SRC . 'apps' . DIRECTORY_SEPARATOR);


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
}

if (!IS_WEB_APP) {

    [$app_path] = get_included_files();
    $script_name = pathinfo($app_path, PATHINFO_BASENAME);
    $script_dir = pathinfo($app_path, PATHINFO_DIRNAME);
    $appName = pathinfo($script_name)['filename'];
    $src_root = '';
    $script_root = '';

    define('FRAMEWORK', 'framework');

    define('APP_CWD', IS_PHAR_APP ? getcwd() . DIRECTORY_SEPARATOR : str_replace($script_name, '', $app_path));

    define('IS_INNER_APP', false !== strpos(APP_CWD, 'ephect' . DIRECTORY_SEPARATOR . FRAMEWORK . DIRECTORY_SEPARATOR . 'apps'));
    define('IS_TASK_APP', false !== strpos(APP_CWD . $script_name, 'ephect' . DIRECTORY_SEPARATOR . FRAMEWORK . DIRECTORY_SEPARATOR . 'bootstrap.php'));
    define('IS_BIN_APP', false !== strpos(APP_CWD . $script_name, 'bin' . DIRECTORY_SEPARATOR . $script_name));

    if (IS_INNER_APP) {
        $script_root = dirname(APP_CWD) . DIRECTORY_SEPARATOR;
        $src_root =dirname(dirname(dirname($script_root))) . DIRECTORY_SEPARATOR  . 'src' . DIRECTORY_SEPARATOR;

        $path = explode(DIRECTORY_SEPARATOR, APP_CWD);
        array_pop($path);
        array_pop($path);
        $appName = array_pop($path);
    } elseif (IS_TASK_APP) {
        $script_root = '.' . DIRECTORY_SEPARATOR;
        $src_root = dirname(APP_CWD) . DIRECTORY_SEPARATOR  . 'src' . DIRECTORY_SEPARATOR;
    } elseif (IS_BIN_APP) {
        $script_root = '.' . DIRECTORY_SEPARATOR;
        $src_root = dirname(APP_CWD) . DIRECTORY_SEPARATOR  . 'src' . DIRECTORY_SEPARATOR;
    } 
    elseif (IS_PHAR_APP) {
        $script_root = '.' . DIRECTORY_SEPARATOR;
        $src_root = APP_CWD  . 'src' . DIRECTORY_SEPARATOR;
    }

    define('SRC_ROOT', $src_root);
    define('SCRIPT_ROOT', $script_root);
    define('SITE_ROOT', dirname(SRC_ROOT) . DIRECTORY_SEPARATOR);

    define('FRAMEWORK_ROOT', SITE_ROOT .  FRAMEWORK . DIRECTORY_SEPARATOR);

    $vendor_dir = 'vendor' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR;
    $portable_dir = 'framework' . DIRECTORY_SEPARATOR;
    $lib = 'ephect_library.php';

    $framework_dir = $vendor_dir;
    $ephect_vendor_lib = '';
    $ephect_vendor_apps = '';

    define('APP_NAME', $appName);

    $ephect_root = \Phar::running();

    if (!IS_PHAR_APP) {

        if (IS_INNER_APP) {
            if (file_exists(SITE_ROOT . $portable_dir . $lib)) {
                $framework_dir = $portable_dir;
            }
            $ephect_vendor_lib = $framework_dir . 'ephect' . DIRECTORY_SEPARATOR;
            $ephect_vendor_apps = $framework_dir . 'apps' . DIRECTORY_SEPARATOR;

            $ephect_root = SITE_ROOT . $ephect_vendor_lib;
        } else {
            if (file_exists(SITE_ROOT . $portable_dir . $lib)) {
                $framework_dir = $portable_dir;
            }
            $ephect_vendor_lib = $framework_dir . 'ephect' . DIRECTORY_SEPARATOR;
            $ephect_vendor_apps = $framework_dir . 'apps' . DIRECTORY_SEPARATOR;

            $ephect_root = SITE_ROOT . $ephect_vendor_lib;
        }
    }

    define('EPHECT_VENDOR_SRC', $framework_dir);
    define('EPHECT_VENDOR_LIB', $ephect_vendor_lib);
    define('EPHECT_VENDOR_APPS', $ephect_vendor_apps);

    define('EPHECT_APPS_ROOT', SITE_ROOT . EPHECT_VENDOR_APPS);

    define('REQUEST_URI', 'https://localhost/');
    define('REQUEST_METHOD', 'GET');
    define('QUERY_STRING', parse_url(REQUEST_URI, PHP_URL_QUERY));
}


define('EPHECT_VENDOR_WIDGETS', EPHECT_VENDOR_SRC . 'widgets' . DIRECTORY_SEPARATOR);
define('EPHECT_VENDOR_PLUGINS', EPHECT_VENDOR_SRC . 'plugins' . DIRECTORY_SEPARATOR);
define('EPHECT_WIDGETS_ROOT', SITE_ROOT . EPHECT_VENDOR_WIDGETS);
define('EPHECT_PLUGINS_ROOT', SITE_ROOT . EPHECT_VENDOR_PLUGINS);
define('EPHECTJS_VENDOR', EPHECT_VENDOR_SRC . 'phinkjs' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR);
define('EPHECTJS_ROOT', SITE_ROOT . EPHECTJS_VENDOR);

define('APP_DIR', 'app' . DIRECTORY_SEPARATOR);
define('APP_ROOT', SRC_ROOT . APP_DIR);
define('APP_SCRIPTS', APP_ROOT . 'scripts' . DIRECTORY_SEPARATOR);
define('APP_DATA', SRC_ROOT . 'data' . DIRECTORY_SEPARATOR);
define('APP_BUSINESS', APP_ROOT . 'business' . DIRECTORY_SEPARATOR);
define('CONTROLLER_ROOT', APP_ROOT . 'controllers' . DIRECTORY_SEPARATOR);
define('BUSINESS_ROOT', APP_ROOT . 'business' . DIRECTORY_SEPARATOR);
define('MODEL_ROOT', APP_ROOT . 'models' . DIRECTORY_SEPARATOR);
define('REST_ROOT', APP_ROOT . 'rest' . DIRECTORY_SEPARATOR);
define('VIEW_ROOT', APP_ROOT . 'views' . DIRECTORY_SEPARATOR);

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

define('EPHECT_ROOT', FRAMEWORK_ROOT . 'ephect' . DIRECTORY_SEPARATOR);
define('HOOKS_ROOT', FRAMEWORK_ROOT . 'hooks' . DIRECTORY_SEPARATOR);
define('PLUGINS_ROOT', FRAMEWORK_ROOT . 'plugins' . DIRECTORY_SEPARATOR);
define('COMMANDS_ROOT', FRAMEWORK_ROOT . 'commands' . DIRECTORY_SEPARATOR);

define('CLASS_EXTENSION', '.class.php');
define('PREHTML_EXTENSION', '.phtml');
define('CSS_EXTENSION', '.css');
define('JS_EXTENSION', '.js');
