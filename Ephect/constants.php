<?php

$document_root = isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '';

define('DONT_USE_IS_WEB_APP', $document_root !== '');
define('DONT_USE_IS_PHAR_APP', (Phar::running() !== ''));
define('DONT_USE_IS_CLI_APP', (Phar::running() === '') && !DONT_USE_IS_WEB_APP);
const DONT_USE_REL_CONFIG_DIR = 'config' . DIRECTORY_SEPARATOR;
const DONT_USE_REL_CONFIG_APP = 'app';

if (DONT_USE_IS_WEB_APP) {
    define('DONT_USE_DOCUMENT_ROOT', $document_root);

    $site_root = dirname(DONT_USE_DOCUMENT_ROOT) . DIRECTORY_SEPARATOR;

    define('DONT_USE_SITE_ROOT', $site_root);
    define('DONT_USE_CONFIG_DIR', DONT_USE_SITE_ROOT . DONT_USE_REL_CONFIG_DIR);
    define(
        'DONT_USE_CONFIG_FRAMEWORK',
        file_exists(DONT_USE_CONFIG_DIR . 'framework')
            ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'framework'))
            : 'vendor/ephect-io/framework/Ephect'
    );
    define(
        'DONT_USE_CONFIG_APP',
        file_exists(DONT_USE_CONFIG_DIR . DONT_USE_REL_CONFIG_APP)
            ? trim(file_get_contents(DONT_USE_CONFIG_DIR . DONT_USE_REL_CONFIG_APP))
            : DONT_USE_REL_CONFIG_APP
    );
    define('DONT_USE_SRC_ROOT', DONT_USE_SITE_ROOT . DONT_USE_CONFIG_APP . DIRECTORY_SEPARATOR);

    define('DONT_USE_AJIL_CONFIG', trim(file_get_contents(DONT_USE_CONFIG_DIR . 'javascripts')));
    define('DONT_USE_EPHECT_ROOT', DONT_USE_SITE_ROOT . DONT_USE_CONFIG_FRAMEWORK . DIRECTORY_SEPARATOR);
    define('DONT_USE_AJIL_ROOT', DONT_USE_SITE_ROOT . DONT_USE_AJIL_CONFIG . DIRECTORY_SEPARATOR);

    $appname = pathinfo(DONT_USE_SITE_ROOT, PATHINFO_FILENAME);
    define('DONT_USE_APP_NAME', $appname);

    define('DONT_USE_AJIL_VENDOR_SRC', DONT_USE_AJIL_ROOT);
    define('DONT_USE_EPHECT_VENDOR_SRC', DONT_USE_EPHECT_ROOT);
    define('DONT_USE_EPHECT_VENDOR_LIB', DONT_USE_EPHECT_VENDOR_SRC . 'Framework' . DIRECTORY_SEPARATOR);
    define('DONT_USE_EPHECT_VENDOR_APPS', DONT_USE_EPHECT_VENDOR_SRC . 'Apps' . DIRECTORY_SEPARATOR);

    $rewrite_base = '/';

    if (file_exists(DONT_USE_CONFIG_DIR . 'rewrite_base') && $rewrite_base = file_get_contents(DONT_USE_CONFIG_DIR . 'rewrite_base')) {
        $rewrite_base = trim($rewrite_base);
    }
    define('DONT_USE_REWRITE_BASE', $rewrite_base);

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

    define('DONT_USE_HTTP_PROTOCOL', $scheme);
    define('DONT_USE_HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT'] ?? '');
    define('DONT_USE_HTTP_HOST', $_SERVER['HTTP_HOST']);
    define('DONT_USE_HTTP_ORIGIN', $_SERVER['HTTP_ORIGIN'] ?? '');
    define('DONT_USE_HTTP_ACCEPT', $_SERVER['HTTP_ACCEPT'] ?: '');
    define('DONT_USE_HTTP_PORT', $_SERVER['SERVER_PORT']);
    define('DONT_USE_COOKIE', $_COOKIE);
    define('DONT_USE_REQUEST_URI', $_SERVER['REQUEST_URI']);
    define('DONT_USE_REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
    define('DONT_USE_QUERY_STRING', parse_url(DONT_USE_REQUEST_URI, PHP_URL_QUERY) ?: '');
    define('DONT_USE_SERVER_NAME', $_SERVER['SERVER_NAME']);
    define('DONT_USE_SERVER_HOST', DONT_USE_HTTP_PROTOCOL . '://' . DONT_USE_HTTP_HOST);
    define('DONT_USE_SERVER_ROOT', DONT_USE_HTTP_PROTOCOL . '://' . DONT_USE_SERVER_NAME . ((DONT_USE_HTTP_PORT !== '80' && DONT_USE_HTTP_PORT !== '443') ? ':' . DONT_USE_HTTP_PORT : ''));
    define('DONT_USE_BASE_URI', DONT_USE_SERVER_NAME . ((DONT_USE_HTTP_PORT !== '80') ? ':' . DONT_USE_HTTP_PORT : '') . ((DONT_USE_REQUEST_URI !== '') ? DONT_USE_REQUEST_URI : ''));
    define('DONT_USE_FULL_URI', DONT_USE_HTTP_PROTOCOL . '://' . DONT_USE_BASE_URI);
    define('DONT_USE_FULL_SSL_URI', 'https://' . DONT_USE_BASE_URI);

    /**
     * TO BE TESTED ON SUBDIRECTORY
     */
    // $hostPort = explode(':', DONT_USE_HTTP_HOST);
    // $is127 = (($host = array_shift($hostPort) . (isset($hostPort[1]) ? $port = ':' . $hostPort[1] : $port = '') == '127.0.0.1' . $port) ? $hostname = 'localhost' : $hostname = $host) !== $host;
    // $isIndex = (((strpos(DONT_USE_REQUEST_URI, 'index.php')  > -1) ? $requestUri = str_replace('index.php', '', DONT_USE_REQUEST_URI) : $requestUri = DONT_USE_REQUEST_URI) !== DONT_USE_REQUEST_URI);

    // if ($is127 || $isIndex) {
    //     header('Location: //' . $hostname . $port . $requestUri);
    //     exit(302);
    // }
    /**
     * END
     */
}

if (!DONT_USE_IS_WEB_APP) {
    $site_root = (getcwd() ? getcwd() : __DIR__) . DIRECTORY_SEPARATOR;

    [$app_path] = get_included_files();
    $script_name = pathinfo($app_path, PATHINFO_BASENAME);
    $script_dir = pathinfo($app_path, PATHINFO_DIRNAME);
    $appName = pathinfo($script_name)['filename'];
    $script_root = $script_dir . DIRECTORY_SEPARATOR;

    define('DONT_USE_APP_CWD', str_replace($script_name, '', $app_path));
    define('DONT_USE_SITE_ROOT', $script_root);

    define('DONT_USE_CONFIG_DIR', DONT_USE_SITE_ROOT . 'config' . DIRECTORY_SEPARATOR);
    define('DONT_USE_CONFIG_FRAMEWORK', file_exists(DONT_USE_CONFIG_DIR . 'framework') ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'framework')) : 'vendor/ephect-io/framework/Ephect');
    define('DONT_USE_EPHECT_ROOT', DONT_USE_SITE_ROOT . DONT_USE_CONFIG_FRAMEWORK . DIRECTORY_SEPARATOR);
    define('DONT_USE_CONFIG_APP', file_exists(DONT_USE_CONFIG_DIR . 'app') ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'app')) : 'app');
    define('DONT_USE_SRC_ROOT', $script_root . DONT_USE_CONFIG_APP . DIRECTORY_SEPARATOR);

    $vendor_dir = 'vendor' . DIRECTORY_SEPARATOR . 'ephect-io' . DIRECTORY_SEPARATOR;
    $portable_dir = 'Epehct' . DIRECTORY_SEPARATOR;
    $bootstrap = 'bootstrap.php';

    $ephect_dir = $vendor_dir . 'framework' . DIRECTORY_SEPARATOR . 'Ephect' . DIRECTORY_SEPARATOR;
    $ajil_dir = $vendor_dir . 'javascripts' . DIRECTORY_SEPARATOR . 'Ajil' . DIRECTORY_SEPARATOR;
    $ephect_vendor_lib = '';
    $ephect_vendor_apps = '';

    define('DONT_USE_APP_NAME', $appName);

    $ephect_root = Phar::running();

    if (file_exists(DONT_USE_SITE_ROOT . $portable_dir . $bootstrap)) {
        $ephect_dir = $portable_dir;
    }
    $ephect_vendor_lib = $ephect_dir . 'Framework' . DIRECTORY_SEPARATOR;
    $ephect_vendor_apps = $ephect_dir . 'Apps' . DIRECTORY_SEPARATOR;

    $ephect_root = DONT_USE_SITE_ROOT . $ephect_vendor_lib;

    define('DONT_USE_EPHECT_VENDOR_SRC', $ephect_dir);
    define('DONT_USE_AJIL_VENDOR_SRC', $ajil_dir);
    define('DONT_USE_EPHECT_VENDOR_LIB', $ephect_vendor_lib);
    define('DONT_USE_EPHECT_VENDOR_APPS', $ephect_vendor_apps);

    define('DONT_USE_EPHECT_APPS_ROOT', DONT_USE_SITE_ROOT . DONT_USE_EPHECT_VENDOR_APPS);

    define('DONT_USE_REQUEST_URI', 'https://localhost/');
    define('DONT_USE_REQUEST_METHOD', 'GET');
    define('DONT_USE_QUERY_STRING', parse_url(DONT_USE_REQUEST_URI, PHP_URL_QUERY));

    define('DONT_USE_AJIL_ROOT', DONT_USE_SITE_ROOT . DONT_USE_AJIL_VENDOR_SRC);
}

define(
    'DONT_USE_CONFIG_DOCROOT',
    file_exists(DONT_USE_CONFIG_DIR . 'document_root')
        ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'document_root'))
        : 'public'
);
define(
    'DONT_USE_CONFIG_HOSTNAME',
    file_exists(DONT_USE_CONFIG_DIR . 'hostname')
        ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'hostname'))
        : 'localhost'
);
define(
    'DONT_USE_CONFIG_NAMESPACE',
    file_exists(DONT_USE_CONFIG_DIR . 'namespace')
        ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'namespace'))
        : DONT_USE_APP_NAME
);
define(
    'DONT_USE_CONFIG_COMMANDS',
    file_exists(DONT_USE_CONFIG_DIR . 'commands')
        ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'commands'))
        : 'Commands'
);
define(
    'DONT_USE_CONFIG_PAGES',
    file_exists(DONT_USE_CONFIG_DIR . 'pages')
        ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'pages'))
        : 'Pages'
);
define(
    'DONT_USE_CONFIG_LIBRARY',
    file_exists(DONT_USE_CONFIG_DIR . 'library')
        ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'library'))
        : 'Library'
);
define(
    'DONT_USE_CONFIG_COMPONENTS',
    file_exists(DONT_USE_CONFIG_DIR . 'components')
        ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'components'))
        : 'Components'
);
define(
    'DONT_USE_CONFIG_HOOKS',
    file_exists(DONT_USE_CONFIG_DIR . 'hooks')
        ? trim(file_get_contents(DONT_USE_CONFIG_DIR . 'hooks'))
        : 'Hooks'
);

if (!DONT_USE_IS_WEB_APP) {
    define('DONT_USE_DOCUMENT_ROOT', DONT_USE_SITE_ROOT . DONT_USE_CONFIG_DOCROOT . DIRECTORY_SEPARATOR);
}

class Constants
{
    public const REL_CONFIG_DIR = DONT_USE_REL_CONFIG_DIR;
    public const REL_CONFIG_APP = DONT_USE_REL_CONFIG_APP;
    public const IS_WEB_APP = DONT_USE_IS_WEB_APP;
    public const IS_PHAR_APP = DONT_USE_IS_PHAR_APP;
    public const IS_CLI_APP = DONT_USE_IS_CLI_APP;
    public const DOCUMENT_ROOT = DONT_USE_DOCUMENT_ROOT;
    public const SITE_ROOT = DONT_USE_SITE_ROOT;
    public const CONFIG_DIR = DONT_USE_CONFIG_DIR;
    public const CONFIG_FRAMEWORK = DONT_USE_CONFIG_FRAMEWORK;
    public const CONFIG_APP = DONT_USE_CONFIG_APP;
    public const SRC_ROOT = DONT_USE_SRC_ROOT;
    public const AJIL_CONFIG = DONT_USE_AJIL_CONFIG;
    public const EPHECT_ROOT = DONT_USE_EPHECT_ROOT;
    public const AJIL_ROOT = DONT_USE_AJIL_ROOT;
    public const APP_NAME = DONT_USE_APP_NAME;
    public const AJIL_VENDOR_SRC = DONT_USE_AJIL_VENDOR_SRC;
    public const EPHECT_VENDOR_SRC = DONT_USE_EPHECT_VENDOR_SRC;
    public const EPHECT_VENDOR_LIB = DONT_USE_EPHECT_VENDOR_LIB;
    public const EPHECT_VENDOR_APPS = DONT_USE_EPHECT_VENDOR_APPS;
    public const REWRITE_BASE = DONT_USE_REWRITE_BASE;
    public const HTTP_PROTOCOL = DONT_USE_HTTP_PROTOCOL;
    public const HTTP_USER_AGENT = DONT_USE_HTTP_USER_AGENT;
    public const HTTP_HOST = DONT_USE_HTTP_HOST;
    public const HTTP_ORIGIN = DONT_USE_HTTP_ORIGIN;
    public const HTTP_ACCEPT = DONT_USE_HTTP_ACCEPT;
    public const HTTP_PORT = DONT_USE_HTTP_PORT;
    public const COOKIE = DONT_USE_COOKIE;
    public const REQUEST_URI = DONT_USE_REQUEST_URI;
    public const REQUEST_METHOD = DONT_USE_REQUEST_METHOD;
    public const QUERY_STRING = DONT_USE_QUERY_STRING;
    public const SERVER_NAME = DONT_USE_SERVER_NAME;
    public const SERVER_HOST = DONT_USE_SERVER_HOST;
    public const SERVER_ROOT = DONT_USE_SERVER_ROOT;
    public const BASE_URI = DONT_USE_BASE_URI;
    public const FULL_URI = DONT_USE_FULL_URI;
    public const FULL_SSL_URI = DONT_USE_FULL_SSL_URI;
    public const APP_CWD = DONT_USE_APP_CWD;
    public const EPHECT_APPS_ROOT = DONT_USE_EPHECT_APPS_ROOT;
    public const CONFIG_DOCROOT = DONT_USE_CONFIG_DOCROOT;
    public const CONFIG_HOSTNAME = DONT_USE_CONFIG_HOSTNAME;
    public const CONFIG_NAMESPACE = DONT_USE_CONFIG_NAMESPACE;
    public const CONFIG_COMMANDS = DONT_USE_CONFIG_COMMANDS;
    public const CONFIG_PAGES = DONT_USE_CONFIG_PAGES;
    public const CONFIG_LIBRARY = DONT_USE_CONFIG_LIBRARY;
    public const CONFIG_COMPONENTS = DONT_USE_CONFIG_COMPONENTS;
    public const CONFIG_HOOKS = DONT_USE_CONFIG_HOOKS;
    public const REL_RUNTIME_JS_DIR = 'js' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
    public const REL_RUNTIME_CSS_DIR = 'css' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
    public const RUNTIME_JS_DIR = DONT_USE_DOCUMENT_ROOT . self::REL_RUNTIME_JS_DIR;
    public const RUNTIME_CSS_DIR = DONT_USE_DOCUMENT_ROOT . self::REL_RUNTIME_CSS_DIR;


    public const EPHECT_VENDOR_WIDGETS = DONT_USE_EPHECT_VENDOR_SRC . 'Widgets' . DIRECTORY_SEPARATOR;
    public const EPHECT_VENDOR_PLUGINS = DONT_USE_EPHECT_VENDOR_SRC . 'Modules' . DIRECTORY_SEPARATOR;
    public const EPHECT_WIDGETS_ROOT = DONT_USE_SITE_ROOT . self::EPHECT_VENDOR_WIDGETS;
    public const EPHECT_PLUGINS_ROOT = DONT_USE_SITE_ROOT . self::EPHECT_VENDOR_PLUGINS;

    public const APP_DIR = DONT_USE_CONFIG_APP . DIRECTORY_SEPARATOR;
    public const APP_ROOT = DONT_USE_SRC_ROOT;
    public const APP_SCRIPTS = self::APP_ROOT . 'scripts' . DIRECTORY_SEPARATOR;
    public const APP_CLIENT = self::APP_ROOT . 'client' . DIRECTORY_SEPARATOR;
    public const APP_DATA = DONT_USE_SITE_ROOT . 'data' . DIRECTORY_SEPARATOR;
    public const APP_BUSINESS = self::APP_ROOT . 'business' . DIRECTORY_SEPARATOR;
    public const CONTROLLER_ROOT = self::APP_ROOT . 'controllers' . DIRECTORY_SEPARATOR;
    public const BUSINESS_ROOT = self::APP_ROOT . 'business' . DIRECTORY_SEPARATOR;
    public const MODEL_ROOT = self::APP_ROOT . 'models' . DIRECTORY_SEPARATOR;
    public const REST_ROOT = self::APP_ROOT . 'rest' . DIRECTORY_SEPARATOR;

    public const VIEW_ROOT = self::APP_ROOT . 'views' . DIRECTORY_SEPARATOR;

    public const REL_RUNTIME_DIR = 'runtime' . DIRECTORY_SEPARATOR;
    public const RUNTIME_DIR = DONT_USE_SITE_ROOT . self::REL_RUNTIME_DIR;
    public const REL_CACHE_DIR = 'cache' . DIRECTORY_SEPARATOR;
    public const CACHE_DIR = DONT_USE_SITE_ROOT . self::REL_CACHE_DIR;
    public const REL_BUILD_DIR = 'build' . DIRECTORY_SEPARATOR;
    public const BUILD_DIR = self::CACHE_DIR . self::REL_BUILD_DIR;
    public const REL_STATIC_DIR = 'static' . DIRECTORY_SEPARATOR;
    public const STATIC_DIR = self::CACHE_DIR . self::REL_STATIC_DIR;
    public const REL_STORE_DIR = 'store' . DIRECTORY_SEPARATOR;
    public const STORE_DIR = self::CACHE_DIR . self::REL_STORE_DIR;
    public const REL_COPY_DIR = 'copy' . DIRECTORY_SEPARATOR;
    public const COPY_DIR = self::CACHE_DIR . self::REL_COPY_DIR;
    public const REL_UNIQUE_DIR = 'unique' . DIRECTORY_SEPARATOR;
    public const UNIQUE_DIR = self::CACHE_DIR . self::REL_UNIQUE_DIR;
    public const LOG_PATH = DONT_USE_SITE_ROOT . 'logs' . DIRECTORY_SEPARATOR;
    public const INFO_LOG = self::LOG_PATH . 'info.log';
    public const DEBUG_LOG = self::LOG_PATH . 'debug.log';
    public const ERROR_LOG = self::LOG_PATH . 'error.log';
    public const SQL_LOG = self::LOG_PATH . 'sql.log';
    public const ROUTES_JSON = self::RUNTIME_DIR . 'routes.json';
    public const FRAMEWORK_ROOT = DONT_USE_EPHECT_ROOT . 'Framework' . DIRECTORY_SEPARATOR;
    public const HOOKS_DIR = 'Hooks';
    public const HOOKS_ROOT = DONT_USE_EPHECT_ROOT . self::HOOKS_DIR . DIRECTORY_SEPARATOR;
    public const PLUGINS_ROOT = DONT_USE_EPHECT_ROOT . 'Plugins' . DIRECTORY_SEPARATOR;
    public const COMMANDS_ROOT = DONT_USE_EPHECT_ROOT . 'Commands' . DIRECTORY_SEPARATOR;
    public const CUSTOM_COMMANDS_ROOT = DONT_USE_SRC_ROOT . DONT_USE_CONFIG_COMMANDS . DIRECTORY_SEPARATOR;
    public const CUSTOM_PAGES_ROOT = DONT_USE_SRC_ROOT . DONT_USE_CONFIG_PAGES . DIRECTORY_SEPARATOR;
    public const CUSTOM_COMPONENTS_ROOT = DONT_USE_SRC_ROOT . DONT_USE_CONFIG_COMPONENTS . DIRECTORY_SEPARATOR;
    public const CUSTOM_HOOKS_ROOT = DONT_USE_SRC_ROOT . DONT_USE_CONFIG_HOOKS . DIRECTORY_SEPARATOR;

    public const CLASS_EXTENSION = '.class.php';
    public const HTML_EXTENSION = '.html';
    public const PREHTML_EXTENSION = '.phtml';
    public const CSS_EXTENSION = '.css';
    public const JS_EXTENSION = '.js';
    public const JSON_EXTENSION = '.json';
    public const CLASS_JS_EXTENSION = '.class.js';
    public const TS_EXTENSION = '.ts';
    public const CLASS_TS_EXTENSION = '.class.ts';
    public const MJS_EXTENSION = '.mjs';
    public const TPL_EXTENSION = '.tpl';
    public const TXT_EXTENSION = '.txt';
}
