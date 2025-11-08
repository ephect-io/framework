<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class ConstantsMaker
{
    private array $constants = [];

    private bool $isWebApp = false;

    public function __construct()
    {
        $this->define();
    }

    private function define()
    {
        // TODO: Implement __invoke() method.
        $document_root = isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '';

        $this->isWebApp = $document_root !== '';
        $this->constants['DONT_USE_IS_WEB_APP'] = $this->isWebApp;
        $this->constants['DONT_USE_IS_PHAR_APP'] = false; //(Phar::running() !== '');
        //        $this->constants['DONT_USE_IS_CLI_APP'] = (Phar::running() === '') && !DONT_USE_IS_WEB_APP;
        $this->constants['DONT_USE_IS_CLI_APP'] = !$this->isWebApp;
        $this->constants['DONT_USE_REL_CONFIG_DIR'] = 'config' . DIRECTORY_SEPARATOR;
        $this->constants['DONT_USE_REL_CONFIG_APP'] = 'app';

        if ($this->isWebApp) {
            $this->constants['DONT_USE_DOCUMENT_ROOT'] = $document_root;

            $site_root = dirname($this->constants['DONT_USE_DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR;

            $this->constants['DONT_USE_SITE_ROOT'] = $site_root;
            $this->constants['DONT_USE_CONFIG_DIR'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['DONT_USE_REL_CONFIG_DIR'];
            $this->constants['DONT_USE_CONFIG_FRAMEWORK'] = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'framework')
                    ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'framework'))
                    : 'vendor/ephect-io/framework/Ephect'
            );

            $this->constants['DONT_USE_AJIL_CONFIG'] = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'javascripts')
                    ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'javascripts'))
                    : 'vendor/ephect-io/javascripts/Ajil'
            );

            $this->constants['DONT_USE_CONFIG_APP'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . $this->constants['DONT_USE_REL_CONFIG_APP'])
                ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . $this->constants['DONT_USE_REL_CONFIG_APP']))
                : $this->constants['DONT_USE_REL_CONFIG_APP'];
            $this->constants['DONT_USE_SRC_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['DONT_USE_CONFIG_APP'] . DIRECTORY_SEPARATOR;

            $this->constants['DONT_USE_EPHECT_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['DONT_USE_CONFIG_FRAMEWORK'] . DIRECTORY_SEPARATOR;
            $this->constants['DONT_USE_AJIL_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['DONT_USE_AJIL_CONFIG'] . DIRECTORY_SEPARATOR;

            $appname = pathinfo($this->constants['DONT_USE_SITE_ROOT'], PATHINFO_FILENAME);
            $this->constants['DONT_USE_APP_NAME'] = $appname;

            $this->constants['DONT_USE_AJIL_VENDOR_SRC'] = $this->constants['DONT_USE_AJIL_ROOT'];
            $this->constants['DONT_USE_EPHECT_VENDOR_SRC'] = $this->constants['DONT_USE_EPHECT_ROOT'];
            $this->constants['DONT_USE_EPHECT_VENDOR_LIB'] = $this->constants['DONT_USE_EPHECT_VENDOR_SRC'] . 'Framework' . DIRECTORY_SEPARATOR;
            $this->constants['DONT_USE_EPHECT_VENDOR_APPS'] = $this->constants['DONT_USE_EPHECT_VENDOR_SRC'] . 'Apps' . DIRECTORY_SEPARATOR;

            $rewrite_base = '/';

            if (file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'DONT_USE_REWRITE_BASE') && $rewrite_base = file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'DONT_USE_REWRITE_BASE')) {
                $rewrite_base = trim($rewrite_base);
            }
            $this->constants['DONT_USE_REWRITE_BASE'] = $rewrite_base;

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

            $this->constants['DONT_USE_HTTP_PROTOCOL'] = $scheme;
            $this->constants['DONT_USE_HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $this->constants['DONT_USE_HTTP_HOST'] = $_SERVER['HTTP_HOST'];
            $this->constants['DONT_USE_HTTP_ORIGIN'] = $_SERVER['HTTP_ORIGIN'] ?? '';
            $this->constants['DONT_USE_HTTP_ACCEPT'] = $_SERVER['HTTP_ACCEPT'] ?: '';
            $this->constants['DONT_USE_HTTP_PORT'] = $_SERVER['SERVER_PORT'];
            $this->constants['DONT_USE_COOKIE'] = $_COOKIE;
            $this->constants['DONT_USE_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $this->constants['DONT_USE_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
            $this->constants['DONT_USE_QUERY_STRING'] = parse_url($this->constants['DONT_USE_REQUEST_URI'], PHP_URL_QUERY) ?: '';
            $this->constants['DONT_USE_SERVER_NAME'] = $_SERVER['SERVER_NAME'];
            $this->constants['DONT_USE_SERVER_HOST'] = $this->constants['DONT_USE_HTTP_PROTOCOL'] . '://' . $this->constants['DONT_USE_HTTP_HOST'];
            $this->constants['DONT_USE_SERVER_ROOT'] = $this->constants['DONT_USE_HTTP_PROTOCOL'] . '://' . $this->constants['DONT_USE_SERVER_NAME'] . (($this->constants['DONT_USE_HTTP_PORT'] !== '80' && $this->constants['DONT_USE_HTTP_PORT'] !== '443') ? ':' . $this->constants['DONT_USE_HTTP_PORT'] : '');
            $this->constants['DONT_USE_BASE_URI'] = $this->constants['DONT_USE_SERVER_NAME'] . (($this->constants['DONT_USE_HTTP_PORT'] !== '80') ? ':' . $this->constants['DONT_USE_HTTP_PORT'] : '') . (($this->constants['DONT_USE_REQUEST_URI'] !== '') ? $this->constants['DONT_USE_REQUEST_URI'] : '');
            $this->constants['DONT_USE_FULL_URI'] = $this->constants['DONT_USE_HTTP_PROTOCOL'] . '://' . $this->constants['DONT_USE_BASE_URI'];
            $this->constants['DONT_USE_FULL_SSL_URI'] = 'https://' . $this->constants['DONT_USE_BASE_URI'];
        }

        if (!$this->isWebApp) {
            [$app_path] = get_included_files();
            $script_name = pathinfo($app_path, PATHINFO_BASENAME);
            $script_dir = pathinfo($app_path, PATHINFO_DIRNAME);
            $appName = pathinfo($script_name)['filename'];
            $script_root = $script_dir . DIRECTORY_SEPARATOR;

            $this->constants['DONT_USE_APP_CWD'] = str_replace($script_name, '', $app_path);
            $this->constants['DONT_USE_SITE_ROOT'] = $script_root;

            $this->constants['DONT_USE_CONFIG_DIR'] = $this->constants['DONT_USE_SITE_ROOT'] . 'config' . DIRECTORY_SEPARATOR;
            $this->constants['DONT_USE_CONFIG_FRAMEWORK'] = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'framework')
                    ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'framework'))
                    : 'vendor/ephect-io/framework/Ephect'
            );

            $this->constants['DONT_USE_EPHECT_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['DONT_USE_CONFIG_FRAMEWORK'] . DIRECTORY_SEPARATOR;
            $this->constants['DONT_USE_CONFIG_APP'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'app') ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'app')) : 'app';
            $this->constants['DONT_USE_SRC_ROOT'] = $script_root . $this->constants['DONT_USE_CONFIG_APP'] . DIRECTORY_SEPARATOR;

            $vendor_dir = 'vendor' . DIRECTORY_SEPARATOR . 'ephect-io' . DIRECTORY_SEPARATOR;
            $portable_dir = 'Epehct' . DIRECTORY_SEPARATOR;
            $bootstrap = 'bootstrap.php';

            $ephect_dir = $vendor_dir . 'framework' . DIRECTORY_SEPARATOR . 'Ephect' . DIRECTORY_SEPARATOR;
            $ajil_dir = $vendor_dir . 'javascripts' . DIRECTORY_SEPARATOR . 'Ajil' . DIRECTORY_SEPARATOR;

            $this->constants['DONT_USE_APP_NAME'] = $appName;

            if (file_exists($this->constants['DONT_USE_SITE_ROOT'] . $portable_dir . $bootstrap)) {
                $ephect_dir = $portable_dir;
            }
            $ephect_vendor_lib = $ephect_dir . 'Framework' . DIRECTORY_SEPARATOR;
            $ephect_vendor_apps = $ephect_dir . 'Apps' . DIRECTORY_SEPARATOR;

            $this->constants['DONT_USE_EPHECT_VENDOR_SRC'] = $ephect_dir;
            $this->constants['DONT_USE_AJIL_VENDOR_SRC'] = $ajil_dir;
            $this->constants['DONT_USE_EPHECT_VENDOR_LIB'] = $ephect_vendor_lib;
            $this->constants['DONT_USE_EPHECT_VENDOR_APPS'] = $ephect_vendor_apps;

            $this->constants['DONT_USE_EPHECT_APPS_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['DONT_USE_EPHECT_VENDOR_APPS'];

            $this->constants['DONT_USE_REQUEST_URI'] = 'https://localhost/';
            $this->constants['DONT_USE_REQUEST_METHOD'] = 'GET';
            $this->constants['DONT_USE_QUERY_STRING'] = parse_url($this->constants['DONT_USE_REQUEST_URI'], PHP_URL_QUERY);

            $this->constants['DONT_USE_AJIL_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['DONT_USE_AJIL_VENDOR_SRC'];
        }

        $this->constants['DONT_USE_CONFIG_DOCROOT'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'DONT_USE_DOCUMENT_ROOT') ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'DONT_USE_DOCUMENT_ROOT')) : 'public';
        $this->constants['DONT_USE_CONFIG_HOSTNAME'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'hostname') ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'hostname')) : 'localhost';
        $this->constants['DONT_USE_CONFIG_NAMESPACE'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'namespace') ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'namespace')) : $this->constants['DONT_USE_APP_NAME'];
        $this->constants['DONT_USE_CONFIG_COMMANDS'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'commands') ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'commands')) : 'Commands';
        $this->constants['DONT_USE_CONFIG_PAGES'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'pages') ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'pages')) : 'Pages';
        $this->constants['DONT_USE_CONFIG_LIBRARY'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'library') ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'library')) : 'Library';
        $this->constants['DONT_USE_CONFIG_COMPONENTS'] = file_exists($this->constants['DONT_USE_CONFIG_DIR'] . 'components') ? trim(file_get_contents($this->constants['DONT_USE_CONFIG_DIR'] . 'components')) : 'Components';

        if (!$this->isWebApp) {
            $this->constants['DONT_USE_DOCUMENT_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['DONT_USE_CONFIG_DOCROOT'] . DIRECTORY_SEPARATOR;
        }
        $this->constants['REL_RUNTIME_JS_DIR'] = 'js' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
        $this->constants['REL_RUNTIME_CSS_DIR'] = 'css' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
        $this->constants['RUNTIME_JS_DIR'] = $this->constants['DONT_USE_DOCUMENT_ROOT'] . $this->constants['REL_RUNTIME_JS_DIR'];
        $this->constants['RUNTIME_CSS_DIR'] = $this->constants['DONT_USE_DOCUMENT_ROOT'] . $this->constants['REL_RUNTIME_CSS_DIR'];


        $this->constants['EPHECT_VENDOR_WIDGETS'] = $this->constants['DONT_USE_EPHECT_VENDOR_SRC'] . 'Widgets' . DIRECTORY_SEPARATOR;
        $this->constants['EPHECT_VENDOR_PLUGINS'] = $this->constants['DONT_USE_EPHECT_VENDOR_SRC'] . 'Modules' . DIRECTORY_SEPARATOR;
        $this->constants['EPHECT_WIDGETS_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['EPHECT_VENDOR_WIDGETS'];
        $this->constants['EPHECT_PLUGINS_ROOT'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['EPHECT_VENDOR_PLUGINS'];

        $this->constants['APP_DIR'] = $this->constants['DONT_USE_CONFIG_APP'] . DIRECTORY_SEPARATOR;
        $this->constants['APP_ROOT'] = $this->constants['DONT_USE_SRC_ROOT'];
        $this->constants['APP_SCRIPTS'] = $this->constants['APP_ROOT'] . 'scripts' . DIRECTORY_SEPARATOR;
        $this->constants['APP_CLIENT'] = $this->constants['APP_ROOT'] . 'client' . DIRECTORY_SEPARATOR;
        $this->constants['APP_DATA'] = $this->constants['DONT_USE_SITE_ROOT'] . 'data' . DIRECTORY_SEPARATOR;
        $this->constants['APP_BUSINESS'] = $this->constants['APP_ROOT'] . 'business' . DIRECTORY_SEPARATOR;
        $this->constants['CONTROLLER_ROOT'] = $this->constants['APP_ROOT'] . 'controllers' . DIRECTORY_SEPARATOR;
        $this->constants['BUSINESS_ROOT'] = $this->constants['APP_ROOT'] . 'business' . DIRECTORY_SEPARATOR;
        $this->constants['MODEL_ROOT'] = $this->constants['APP_ROOT'] . 'models' . DIRECTORY_SEPARATOR;
        $this->constants['REST_ROOT'] = $this->constants['APP_ROOT'] . 'rest' . DIRECTORY_SEPARATOR;
        $this->constants['VIEW_ROOT'] = $this->constants['APP_ROOT'] . 'views' . DIRECTORY_SEPARATOR;

        $this->constants['REL_RUNTIME_DIR'] = 'runtime' . DIRECTORY_SEPARATOR;
        $this->constants['RUNTIME_DIR'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['REL_RUNTIME_DIR'];
        $this->constants['REL_CACHE_DIR'] = 'cache' . DIRECTORY_SEPARATOR;
        $this->constants['CACHE_DIR'] = $this->constants['DONT_USE_SITE_ROOT'] . $this->constants['REL_CACHE_DIR'];
        $this->constants['REL_STATIC_DIR'] = 'static' . DIRECTORY_SEPARATOR;
        $this->constants['STATIC_DIR'] = $this->constants['CACHE_DIR'] . $this->constants['REL_STATIC_DIR'];
        $this->constants['REL_STORE_DIR'] = 'store' . DIRECTORY_SEPARATOR;
        $this->constants['STORE_DIR'] = $this->constants['CACHE_DIR'] . $this->constants['REL_STORE_DIR'];
        $this->constants['REL_COPY_DIR'] = 'copy' . DIRECTORY_SEPARATOR;
        $this->constants['COPY_DIR'] = $this->constants['CACHE_DIR'] . $this->constants['REL_COPY_DIR'];
        $this->constants['REL_UNIQUE_DIR'] = 'unique' . DIRECTORY_SEPARATOR;
        $this->constants['UNIQUE_DIR'] = $this->constants['CACHE_DIR'] . $this->constants['REL_UNIQUE_DIR'];
        $this->constants['LOG_PATH'] = $this->constants['DONT_USE_SITE_ROOT'] . 'logs' . DIRECTORY_SEPARATOR;
        $this->constants['INFO_LOG'] = $this->constants['LOG_PATH'] . 'info.log';
        $this->constants['DEBUG_LOG'] = $this->constants['LOG_PATH'] . 'debug.log';
        $this->constants['ERROR_LOG'] = $this->constants['LOG_PATH'] . 'error.log';
        $this->constants['SQL_LOG'] = $this->constants['LOG_PATH'] . 'sql.log';
        $this->constants['ROUTES_JSON'] = $this->constants['RUNTIME_DIR'] . 'routes.json';

        $this->constants['FRAMEWORK_ROOT'] = $this->constants['DONT_USE_EPHECT_ROOT'] . 'Framework' . DIRECTORY_SEPARATOR;
        $this->constants['HOOKS_ROOT'] = $this->constants['DONT_USE_EPHECT_ROOT'] . 'Hooks' . DIRECTORY_SEPARATOR;
        $this->constants['PLUGINS_ROOT'] = $this->constants['DONT_USE_EPHECT_ROOT'] . 'Plugins' . DIRECTORY_SEPARATOR;
        $this->constants['COMMANDS_ROOT'] = $this->constants['DONT_USE_EPHECT_ROOT'] . 'Commands' . DIRECTORY_SEPARATOR;
        $this->constants['CUSTOM_COMMANDS_ROOT'] = $this->constants['DONT_USE_SRC_ROOT'] . $this->constants['DONT_USE_CONFIG_COMMANDS'] . DIRECTORY_SEPARATOR;
        $this->constants['CUSTOM_PAGES_ROOT'] = $this->constants['DONT_USE_SRC_ROOT'] . $this->constants['DONT_USE_CONFIG_PAGES'] . DIRECTORY_SEPARATOR;
        $this->constants['CUSTOM_COMPONENTS_ROOT'] = $this->constants['DONT_USE_SRC_ROOT'] . $this->constants['DONT_USE_CONFIG_COMPONENTS'] . DIRECTORY_SEPARATOR;

        $this->constants['CLASS_EXTENSION'] = '.class.php';
        $this->constants['HTML_EXTENSION'] = '.html';
        $this->constants['PREHTML_EXTENSION'] = '.phtml';
        $this->constants['CSS_EXTENSION'] = '.css';
        $this->constants['JS_EXTENSION'] = '.js';
        $this->constants['TS_EXTENSION'] = '.ts';
        $this->constants['JSON_EXTENSION'] = '.json';
        $this->constants['CLASS_JS_EXTENSION'] = '.class.js';
        $this->constants['CLASS_TS_EXTENSION'] = '.class.ts';
        $this->constants['MJS_EXTENSION'] = '.mjs';
        $this->constants['TPL_EXTENSION'] = '.tpl';
        $this->constants['TXT_EXTENSION'] = '.txt';
    }

    public function list(): array
    {
        return $this->constants;
    }

    public function log(): void
    {
        foreach ($this->constants as $key => $value) {
            Console::Log($key . ' => ' . $value);
        }
    }

    public function make()
    {
        $filename = $this->constants['DONT_USE_EPHECT_ROOT'] . ($this->isWebApp ? 'web' : 'cli') . '_constants.php';

        $lines = "<?php" . PHP_EOL;
        foreach ($this->constants as $key => $value) {
            if (is_bool($value)) {
                $lines .= "const " . $key . " = " . ($value ? 'true' : 'false') . ";" . PHP_EOL;
            } elseif (is_int($value) || is_float($value)) {
                $lines .= "const " . $key . " = " . $value . ";" . PHP_EOL;
            } elseif (is_array($value)) {
                $lines .= "const " . $key . " = " . Text::arrayToString($value, true) . ";" . PHP_EOL;
            } else {
                $lines .= "const " . $key . " = '" . str_replace('\\', '\\\\', $value) . "';" . PHP_EOL;
            }
        }
        $lines .= PHP_EOL;

        File::safeWrite($filename, $lines);

        include $filename;
    }

};
