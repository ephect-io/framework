<?php

$document_root = isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '';

define('IS_WEB_APP', $document_root !== '');
define('IS_PHAR_APP', (Phar::running() !== ''));
define('IS_CLI_APP', (Phar::running() === '') && !IS_WEB_APP);
const REL_CONFIG_DIR = 'config' . DIRECTORY_SEPARATOR;
const REL_CONFIG_APP = 'app';

if (IS_WEB_APP) {

    define('DOCUMENT_ROOT', $document_root);

    $site_root = dirname(DOCUMENT_ROOT) . DIRECTORY_SEPARATOR;

    define('SITE_ROOT', $site_root);
    define('CONFIG_DIR', SITE_ROOT . REL_CONFIG_DIR);
    define('CONFIG_FRAMEWORK', file_exists(CONFIG_DIR . 'framework') ? trim(file_get_contents(CONFIG_DIR . 'framework')) : 'vendor/ephect-io/framework/Ephect');
    define('CONFIG_APP', file_exists(CONFIG_DIR . REL_CONFIG_APP) ? trim(file_get_contents(CONFIG_DIR . REL_CONFIG_APP)) : REL_CONFIG_APP);
    define('SRC_ROOT', SITE_ROOT . CONFIG_APP . DIRECTORY_SEPARATOR);

    define('AJIL_CONFIG', trim(file_get_contents(CONFIG_DIR . 'javascripts')));
    define('EPHECT_ROOT', SITE_ROOT . CONFIG_FRAMEWORK . DIRECTORY_SEPARATOR);
    define('AJIL_ROOT', SITE_ROOT . AJIL_CONFIG . DIRECTORY_SEPARATOR);

    $appname = pathinfo(SITE_ROOT, PATHINFO_FILENAME);
    define('APP_NAME', $appname);

    define('AJIL_VENDOR_SRC', AJIL_ROOT);
    define('EPHECT_VENDOR_SRC', EPHECT_ROOT);
    define('EPHECT_VENDOR_LIB', EPHECT_VENDOR_SRC . 'Framework' . DIRECTORY_SEPARATOR);
    define('EPHECT_VENDOR_APPS', EPHECT_VENDOR_SRC . 'Apps' . DIRECTORY_SEPARATOR);

    $rewrite_base = '/';

    if (file_exists(CONFIG_DIR . 'rewrite_base') && $rewrite_base = file_get_contents(CONFIG_DIR . 'rewrite_base')) {
        $rewrite_base = trim($rewrite_base);
    }
    define('REWRITE_BASE', $rewrite_base);

    $scheme = 'http';
    if (str_contains($_SERVER['SERVER_SOFTWARE'], 'IIS')) {
        $scheme = ($_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
    } elseif (str_contains($_SERVER['SERVER_SOFTWARE'], 'Apache')) {
        $scheme = $_SERVER['REQUEST_SCHEME'];
    } elseif (str_contains($_SERVER['SERVER_SOFTWARE'], 'lighttpd')) {
        $scheme = str_contains($_SERVER['SERVER_PROTOCOL'], 'HTPPS') ? 'https' : 'http';
    } elseif (str_contains($_SERVER['SERVER_SOFTWARE'], 'nginx')) {
        $scheme = str_contains($_SERVER['SERVER_PROTOCOL'], 'HTPPS') ? 'https' : 'http';
    }

    define('HTTP_PROTOCOL', $scheme);
    define('HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT'] ?? '');
    define('HTTP_HOST', $_SERVER['HTTP_HOST']);
    define('HTTP_ORIGIN', $_SERVER['HTTP_ORIGIN'] ?? '');
    define('HTTP_ACCEPT', $_SERVER['HTTP_ACCEPT'] ?: '');
    define('HTTP_PORT', $_SERVER['SERVER_PORT']);
    define('COOKIE', $_COOKIE);
    define('REQUEST_URI', $_SERVER['REQUEST_URI']);
    define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
    define('QUERY_STRING', parse_url(REQUEST_URI, PHP_URL_QUERY) ?: '');
    define('SERVER_NAME', $_SERVER['SERVER_NAME']);
    define('SERVER_HOST', HTTP_PROTOCOL . '://' . HTTP_HOST);
    define('SERVER_ROOT', HTTP_PROTOCOL . '://' . SERVER_NAME . ((HTTP_PORT !== '80' && HTTP_PORT !== '443') ? ':' . HTTP_PORT : ''));
    define('BASE_URI', SERVER_NAME . ((HTTP_PORT !== '80') ? ':' . HTTP_PORT : '') . ((REQUEST_URI !== '') ? REQUEST_URI : ''));
    define('FULL_URI', HTTP_PROTOCOL . '://' . BASE_URI);
    define('FULL_SSL_URI', 'https://' . BASE_URI);

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

    $site_root = (getcwd() ? getcwd() : __DIR__) . DIRECTORY_SEPARATOR;

    [$app_path] = get_included_files();
    $script_name = pathinfo($app_path, PATHINFO_BASENAME);
    $script_dir = pathinfo($app_path, PATHINFO_DIRNAME);
    $appName = pathinfo($script_name)['filename'];
    $script_root = $script_dir . DIRECTORY_SEPARATOR;

    define('APP_CWD', str_replace($script_name, '', $app_path));
    define('SITE_ROOT', $script_root);

    define('CONFIG_DIR', SITE_ROOT . 'config' . DIRECTORY_SEPARATOR);
    define('CONFIG_FRAMEWORK', file_exists(CONFIG_DIR . 'framework') ? trim(file_get_contents(CONFIG_DIR . 'framework')) : 'vendor/ephect-io/framework/Ephect');
    define('EPHECT_ROOT', SITE_ROOT . CONFIG_FRAMEWORK . DIRECTORY_SEPARATOR);
    define('CONFIG_APP', file_exists(CONFIG_DIR . 'app') ? trim(file_get_contents(CONFIG_DIR . 'app')) : 'app');
    define('SRC_ROOT', $script_root . CONFIG_APP . DIRECTORY_SEPARATOR);

    $vendor_dir = 'vendor' . DIRECTORY_SEPARATOR . 'ephect-io' . DIRECTORY_SEPARATOR;
    $portable_dir = 'Epehct' . DIRECTORY_SEPARATOR;
    $bootstrap = 'bootstrap.php';

    $ephect_dir = $vendor_dir . 'framework' . DIRECTORY_SEPARATOR . 'Ephect' . DIRECTORY_SEPARATOR;
    $ajil_dir = $vendor_dir . 'javascripts' . DIRECTORY_SEPARATOR . 'Ajil' . DIRECTORY_SEPARATOR;
    $ephect_vendor_lib = '';
    $ephect_vendor_apps = '';

    define('APP_NAME', $appName);

    $ephect_root = Phar::running();

    if (!IS_PHAR_APP) {

        if (file_exists(SITE_ROOT . $portable_dir . $bootstrap)) {
            $ephect_dir = $portable_dir;
        }
        $ephect_vendor_lib = $ephect_dir . 'Framework' . DIRECTORY_SEPARATOR;
        $ephect_vendor_apps = $ephect_dir . 'Apps' . DIRECTORY_SEPARATOR;

        $ephect_root = SITE_ROOT . $ephect_vendor_lib;

    }

    define('EPHECT_VENDOR_SRC', $ephect_dir);
    define('AJIL_VENDOR_SRC', $ajil_dir);
    define('EPHECT_VENDOR_LIB', $ephect_vendor_lib);
    define('EPHECT_VENDOR_APPS', $ephect_vendor_apps);

    define('EPHECT_APPS_ROOT', SITE_ROOT . EPHECT_VENDOR_APPS);

    define('REQUEST_URI', 'https://localhost/');
    define('REQUEST_METHOD', 'GET');
    define('QUERY_STRING', parse_url(REQUEST_URI, PHP_URL_QUERY));

    define('AJIL_ROOT', SITE_ROOT . AJIL_VENDOR_SRC);
}

define('CONFIG_DOCROOT', file_exists(CONFIG_DIR . 'document_root') ? trim(file_get_contents(CONFIG_DIR . 'document_root')) : 'public');
define('CONFIG_HOSTNAME', file_exists(CONFIG_DIR . 'hostname') ? trim(file_get_contents(CONFIG_DIR . 'hostname')) : 'localhost');
define('CONFIG_NAMESPACE', file_exists(CONFIG_DIR . 'namespace') ? trim(file_get_contents(CONFIG_DIR . 'namespace')) : APP_NAME);
define('CONFIG_COMMANDS', file_exists(CONFIG_DIR . 'commands') ? trim(file_get_contents(CONFIG_DIR . 'commands')) : 'Commands');
define('CONFIG_PAGES', file_exists(CONFIG_DIR . 'pages') ? trim(file_get_contents(CONFIG_DIR . 'pages')) : 'Pages');
define('CONFIG_LIBRARY', file_exists(CONFIG_DIR . 'library') ? trim(file_get_contents(CONFIG_DIR . 'library')) : 'Library');
define('CONFIG_COMPONENTS', file_exists(CONFIG_DIR . 'components') ? trim(file_get_contents(CONFIG_DIR . 'components')) : 'Components');

if (!IS_WEB_APP) {
    define('DOCUMENT_ROOT', SITE_ROOT . CONFIG_DOCROOT . DIRECTORY_SEPARATOR);
}

class Constants {

    public const REL_RUNTIME_JS_DIR = 'js' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
    public const REL_RUNTIME_CSS_DIR = 'css' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
    public const RUNTIME_JS_DIR = DOCUMENT_ROOT . self::REL_RUNTIME_JS_DIR;
    public const RUNTIME_CSS_DIR = DOCUMENT_ROOT . self::REL_RUNTIME_CSS_DIR;


    public const EPHECT_VENDOR_WIDGETS = EPHECT_VENDOR_SRC . 'Widgets' . DIRECTORY_SEPARATOR;
    public const EPHECT_VENDOR_PLUGINS = EPHECT_VENDOR_SRC . 'Modules' . DIRECTORY_SEPARATOR;
    public const EPHECT_WIDGETS_ROOT = SITE_ROOT . self::EPHECT_VENDOR_WIDGETS;
    public const EPHECT_PLUGINS_ROOT = SITE_ROOT . self::EPHECT_VENDOR_PLUGINS;

    public const APP_DIR = CONFIG_APP . DIRECTORY_SEPARATOR;
    public const APP_ROOT = SRC_ROOT;
    public const APP_SCRIPTS = self::APP_ROOT . 'scripts' . DIRECTORY_SEPARATOR;
    public const APP_CLIENT = self::APP_ROOT . 'client' . DIRECTORY_SEPARATOR;
    public const APP_DATA = SITE_ROOT . 'data' . DIRECTORY_SEPARATOR;
    public const APP_BUSINESS = self::APP_ROOT . 'business' . DIRECTORY_SEPARATOR;
    public const CONTROLLER_ROOT = self::APP_ROOT . 'controllers' . DIRECTORY_SEPARATOR;
    public const BUSINESS_ROOT = self::APP_ROOT . 'business' . DIRECTORY_SEPARATOR;
    public const MODEL_ROOT = self::APP_ROOT . 'models' . DIRECTORY_SEPARATOR;
    public const REST_ROOT = self::APP_ROOT . 'rest' . DIRECTORY_SEPARATOR;

    public const VIEW_ROOT = self::APP_ROOT . 'views' . DIRECTORY_SEPARATOR;

    public const REL_RUNTIME_DIR = 'runtime' . DIRECTORY_SEPARATOR;
    public const RUNTIME_DIR = SITE_ROOT . self::REL_RUNTIME_DIR;
    public const REL_CACHE_DIR = 'cache' . DIRECTORY_SEPARATOR;
    public const CACHE_DIR = SITE_ROOT . self::REL_CACHE_DIR;
    public const REL_STATIC_DIR = 'static' . DIRECTORY_SEPARATOR;
    public const STATIC_DIR = self::CACHE_DIR . self::REL_STATIC_DIR;
    public const REL_STORE_DIR = 'store' . DIRECTORY_SEPARATOR;
    public const STORE_DIR = self::CACHE_DIR . self::REL_STORE_DIR;
    public const REL_COPY_DIR = 'copy' . DIRECTORY_SEPARATOR;
    public const COPY_DIR = self::CACHE_DIR . self::REL_COPY_DIR;
    public const REL_UNIQUE_DIR = 'unique' . DIRECTORY_SEPARATOR;
    public const UNIQUE_DIR = self::CACHE_DIR . self::REL_UNIQUE_DIR;
    public const LOG_PATH = SITE_ROOT . 'logs' . DIRECTORY_SEPARATOR;
    public const INFO_LOG = self::LOG_PATH . 'info.log';
    public const DEBUG_LOG = self::LOG_PATH . 'debug.log';
    public const ERROR_LOG = self::LOG_PATH . 'error.log';
    public const SQL_LOG = self::LOG_PATH . 'sql.log';
    public const ROUTES_JSON = self::RUNTIME_DIR . 'routes.json';

    public const FRAMEWORK_ROOT = EPHECT_ROOT . 'Framework' . DIRECTORY_SEPARATOR;
    public const HOOKS_ROOT = EPHECT_ROOT . 'Hooks' . DIRECTORY_SEPARATOR;
    public const PLUGINS_ROOT = EPHECT_ROOT . 'Plugins' . DIRECTORY_SEPARATOR;
    public const COMMANDS_ROOT = EPHECT_ROOT . 'Commands' . DIRECTORY_SEPARATOR;
    public const CUSTOM_COMMANDS_ROOT = SRC_ROOT . CONFIG_COMMANDS . DIRECTORY_SEPARATOR;
    public const CUSTOM_PAGES_ROOT = SRC_ROOT . CONFIG_PAGES . DIRECTORY_SEPARATOR;
    public const CUSTOM_COMPONENTS_ROOT = SRC_ROOT . CONFIG_COMPONENTS . DIRECTORY_SEPARATOR;

    public const CLASS_EXTENSION = '.class.php';
    public const HTML_EXTENSION = '.html';
    public const PREHTML_EXTENSION = '.phtml';
    public const CSS_EXTENSION = '.css';
    public const JS_EXTENSION = '.js';
    public const CLASS_JS_EXTENSION = '.class.js';
    public const MJS_EXTENSION = '.mjs';
    public const TPL_EXTENSION = '.tpl';
    public const TXT_EXTENSION = '.txt';

}
