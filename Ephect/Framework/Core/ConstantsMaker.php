<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\Utils\File;
use Ephect\Framework\Utils\Text;

class ConstantsMaker
{
    private array $constants = [];

    public function __construct()
    {
        $this->make();
    }

    private function make()
    {
        // TODO: Implement __invoke() method.
        $document_root = isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '';

        $isWebApp = $document_root !== '';
        $this->constants['IS_WEB_APP'] = $isWebApp;
        $this->constants['IS_PHAR_APP'] = false; //(Phar::running() !== '');
//        $this->constants['IS_CLI_APP'] = (Phar::running() === '') && !IS_WEB_APP;
        $this->constants['IS_CLI_APP'] = !$isWebApp;
        $this->constants['REL_CONFIG_DIR'] = 'config' . DIRECTORY_SEPARATOR;
        $this->constants['REL_CONFIG_APP'] = 'app';

        if ($isWebApp) {
            $this->constants['DOCUMENT_ROOT'] = $document_root;

            $site_root = dirname($this->constants['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR;

            $this->constants['SITE_ROOT'] = $site_root;
            $this->constants['CONFIG_DIR'] = $this->constants['SITE_ROOT'] . $this->constants['REL_CONFIG_DIR'];
            $this->constants['CONFIG_FRAMEWORK'] = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                file_exists($this->constants['CONFIG_DIR'] . 'framework')
                    ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'framework'))
                    : 'vendor/ephect-io/framework/Ephect'
            );

            $this->constants['AJIL_CONFIG'] = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                file_exists($this->constants['CONFIG_DIR'] . 'javascripts')
                    ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'javascripts'))
                    : 'vendor/ephect-io/javascripts/Ajil'
            );

            $this->constants['CONFIG_APP'] = file_exists($this->constants['CONFIG_DIR'] . $this->constants['REL_CONFIG_APP'])
                ? trim(file_get_contents($this->constants['CONFIG_DIR'] . $this->constants['REL_CONFIG_APP']))
                : $this->constants['REL_CONFIG_APP'];
            $this->constants['SRC_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['CONFIG_APP'] . DIRECTORY_SEPARATOR;

            $this->constants['EPHECT_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['CONFIG_FRAMEWORK'] . DIRECTORY_SEPARATOR;
            $this->constants['AJIL_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['AJIL_CONFIG'] . DIRECTORY_SEPARATOR;

            $appname = pathinfo($this->constants['SITE_ROOT'], PATHINFO_FILENAME);
            $this->constants['APP_NAME'] = $appname;

            $this->constants['AJIL_VENDOR_SRC'] = $this->constants['AJIL_ROOT'];
            $this->constants['EPHECT_VENDOR_SRC'] = $this->constants['EPHECT_ROOT'];
            $this->constants['EPHECT_VENDOR_LIB'] = $this->constants['EPHECT_VENDOR_SRC'] . 'Framework' . DIRECTORY_SEPARATOR;
            $this->constants['EPHECT_VENDOR_APPS'] = $this->constants['EPHECT_VENDOR_SRC'] . 'Apps' . DIRECTORY_SEPARATOR;

            $rewrite_base = '/';

            if (file_exists($this->constants['CONFIG_DIR'] . 'rewrite_base') && $rewrite_base = file_get_contents($this->constants['CONFIG_DIR'] . 'rewrite_base')) {
                $rewrite_base = trim($rewrite_base);
            }
            $this->constants['REWRITE_BASE'] = $rewrite_base;

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

            $this->constants['HTTP_PROTOCOL'] = $scheme;
            $this->constants['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $this->constants['HTTP_HOST'] = $_SERVER['HTTP_HOST'];
            $this->constants['HTTP_ORIGIN'] = $_SERVER['HTTP_ORIGIN'] ?? '';
            $this->constants['HTTP_ACCEPT'] = $_SERVER['HTTP_ACCEPT'] ?: '';
            $this->constants['HTTP_PORT'] = $_SERVER['SERVER_PORT'];
            $this->constants['COOKIE'] = $_COOKIE;
            $this->constants['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $this->constants['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
            $this->constants['QUERY_STRING'] = parse_url($this->constants['REQUEST_URI'], PHP_URL_QUERY) ?: '';
            $this->constants['SERVER_NAME'] = $_SERVER['SERVER_NAME'];
            $this->constants['SERVER_HOST'] = $this->constants['HTTP_PROTOCOL'] . '://' . $this->constants['HTTP_HOST'];
            $this->constants['SERVER_ROOT'] = $this->constants['HTTP_PROTOCOL'] . '://' . $this->constants['SERVER_NAME'] . (($this->constants['HTTP_PORT'] !== '80' && $this->constants['HTTP_PORT'] !== '443') ? ':' . $this->constants['HTTP_PORT'] : '');
            $this->constants['BASE_URI'] = $this->constants['SERVER_NAME'] . (($this->constants['HTTP_PORT'] !== '80') ? ':' . $this->constants['HTTP_PORT'] : '') . (($this->constants['REQUEST_URI'] !== '') ? $this->constants['REQUEST_URI'] : '');
            $this->constants['FULL_URI'] = $this->constants['HTTP_PROTOCOL'] . '://' . $this->constants['BASE_URI'];
            $this->constants['FULL_SSL_URI'] = 'https://' . $this->constants['BASE_URI'];
        }

        if (!$isWebApp) {
            [$app_path] = get_included_files();
            $script_name = pathinfo($app_path, PATHINFO_BASENAME);
            $script_dir = pathinfo($app_path, PATHINFO_DIRNAME);
            $appName = pathinfo($script_name)['filename'];
            $script_root = $script_dir . DIRECTORY_SEPARATOR;

            $this->constants['APP_CWD'] = str_replace($script_name, '', $app_path);
            $this->constants['SITE_ROOT'] = $script_root;

            $this->constants['CONFIG_DIR'] = $this->constants['SITE_ROOT'] . 'config' . DIRECTORY_SEPARATOR;
            $this->constants['CONFIG_FRAMEWORK'] = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                file_exists($this->constants['CONFIG_DIR'] . 'framework')
                    ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'framework'))
                    : 'vendor/ephect-io/framework/Ephect'
            );

            $this->constants['EPHECT_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['CONFIG_FRAMEWORK'] . DIRECTORY_SEPARATOR;
            $this->constants['CONFIG_APP'] = file_exists($this->constants['CONFIG_DIR'] . 'app') ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'app')) : 'app';
            $this->constants['SRC_ROOT'] = $script_root . $this->constants['CONFIG_APP'] . DIRECTORY_SEPARATOR;

            $vendor_dir = 'vendor' . DIRECTORY_SEPARATOR . 'ephect-io' . DIRECTORY_SEPARATOR;
            $portable_dir = 'Epehct' . DIRECTORY_SEPARATOR;
            $bootstrap = 'bootstrap.php';

            $ephect_dir = $vendor_dir . 'framework' . DIRECTORY_SEPARATOR . 'Ephect' . DIRECTORY_SEPARATOR;
            $ajil_dir = $vendor_dir . 'javascripts' . DIRECTORY_SEPARATOR . 'Ajil' . DIRECTORY_SEPARATOR;

            $this->constants['APP_NAME'] = $appName;

            if (file_exists($this->constants['SITE_ROOT'] . $portable_dir . $bootstrap)) {
                $ephect_dir = $portable_dir;
            }
            $ephect_vendor_lib = $ephect_dir . 'Framework' . DIRECTORY_SEPARATOR;
            $ephect_vendor_apps = $ephect_dir . 'Apps' . DIRECTORY_SEPARATOR;

            $this->constants['EPHECT_VENDOR_SRC'] = $ephect_dir;
            $this->constants['AJIL_VENDOR_SRC'] = $ajil_dir;
            $this->constants['EPHECT_VENDOR_LIB'] = $ephect_vendor_lib;
            $this->constants['EPHECT_VENDOR_APPS'] = $ephect_vendor_apps;

            $this->constants['EPHECT_APPS_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['EPHECT_VENDOR_APPS'];

            $this->constants['REQUEST_URI'] = 'https://localhost/';
            $this->constants['REQUEST_METHOD'] = 'GET';
            $this->constants['QUERY_STRING'] = parse_url($this->constants['REQUEST_URI'], PHP_URL_QUERY);

            $this->constants['AJIL_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['AJIL_VENDOR_SRC'];
        }

        $this->constants['CONFIG_DOCROOT'] = file_exists($this->constants['CONFIG_DIR'] . 'document_root') ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'document_root')) : 'public';
        $this->constants['CONFIG_HOSTNAME'] = file_exists($this->constants['CONFIG_DIR'] . 'hostname') ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'hostname')) : 'localhost';
        $this->constants['CONFIG_NAMESPACE'] = file_exists($this->constants['CONFIG_DIR'] . 'namespace') ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'namespace')) : $this->constants['APP_NAME'];
        $this->constants['CONFIG_COMMANDS'] = file_exists($this->constants['CONFIG_DIR'] . 'commands') ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'commands')) : 'Commands';
        $this->constants['CONFIG_PAGES'] = file_exists($this->constants['CONFIG_DIR'] . 'pages') ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'pages')) : 'Pages';
        $this->constants['CONFIG_LIBRARY'] = file_exists($this->constants['CONFIG_DIR'] . 'library') ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'library')) : 'Library';
        $this->constants['CONFIG_COMPONENTS'] = file_exists($this->constants['CONFIG_DIR'] . 'components') ? trim(file_get_contents($this->constants['CONFIG_DIR'] . 'components')) : 'Components';

        if (!$isWebApp) {
            $this->constants['DOCUMENT_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['CONFIG_DOCROOT'] . DIRECTORY_SEPARATOR;
        }
        $this->constants['REL_RUNTIME_JS_DIR'] = 'js' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
        $this->constants['REL_RUNTIME_CSS_DIR'] = 'css' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
        $this->constants['RUNTIME_JS_DIR'] = $this->constants['DOCUMENT_ROOT'] . $this->constants['REL_RUNTIME_JS_DIR'];
        $this->constants['RUNTIME_CSS_DIR'] = $this->constants['DOCUMENT_ROOT'] . $this->constants['REL_RUNTIME_CSS_DIR'];


        $this->constants['EPHECT_VENDOR_WIDGETS'] = $this->constants['EPHECT_VENDOR_SRC'] . 'Widgets' . DIRECTORY_SEPARATOR;
        $this->constants['EPHECT_VENDOR_PLUGINS'] = $this->constants['EPHECT_VENDOR_SRC'] . 'Modules' . DIRECTORY_SEPARATOR;
        $this->constants['EPHECT_WIDGETS_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['EPHECT_VENDOR_WIDGETS'];
        $this->constants['EPHECT_PLUGINS_ROOT'] = $this->constants['SITE_ROOT'] . $this->constants['EPHECT_VENDOR_PLUGINS'];

        $this->constants['APP_DIR'] = $this->constants['CONFIG_APP'] . DIRECTORY_SEPARATOR;
        $this->constants['APP_ROOT'] = $this->constants['SRC_ROOT'];
        $this->constants['APP_SCRIPTS'] = $this->constants['APP_ROOT'] . 'scripts' . DIRECTORY_SEPARATOR;
        $this->constants['APP_CLIENT'] = $this->constants['APP_ROOT'] . 'client' . DIRECTORY_SEPARATOR;
        $this->constants['APP_DATA'] = $this->constants['SITE_ROOT'] . 'data' . DIRECTORY_SEPARATOR;
        $this->constants['APP_BUSINESS'] = $this->constants['APP_ROOT'] . 'business' . DIRECTORY_SEPARATOR;
        $this->constants['CONTROLLER_ROOT'] = $this->constants['APP_ROOT'] . 'controllers' . DIRECTORY_SEPARATOR;
        $this->constants['BUSINESS_ROOT'] = $this->constants['APP_ROOT'] . 'business' . DIRECTORY_SEPARATOR;
        $this->constants['MODEL_ROOT'] = $this->constants['APP_ROOT'] . 'models' . DIRECTORY_SEPARATOR;
        $this->constants['REST_ROOT'] = $this->constants['APP_ROOT'] . 'rest' . DIRECTORY_SEPARATOR;
        $this->constants['VIEW_ROOT'] = $this->constants['APP_ROOT'] . 'views' . DIRECTORY_SEPARATOR;

        $this->constants['REL_RUNTIME_DIR'] = 'runtime' . DIRECTORY_SEPARATOR;
        $this->constants['RUNTIME_DIR'] = $this->constants['SITE_ROOT'] . $this->constants['REL_RUNTIME_DIR'];
        $this->constants['REL_CACHE_DIR'] = 'cache' . DIRECTORY_SEPARATOR;
        $this->constants['CACHE_DIR'] = $this->constants['SITE_ROOT'] . $this->constants['REL_CACHE_DIR'];
        $this->constants['REL_STATIC_DIR'] = 'static' . DIRECTORY_SEPARATOR;
        $this->constants['STATIC_DIR'] = $this->constants['CACHE_DIR'] . $this->constants['REL_STATIC_DIR'];
        $this->constants['REL_STORE_DIR'] = 'store' . DIRECTORY_SEPARATOR;
        $this->constants['STORE_DIR'] = $this->constants['CACHE_DIR'] . $this->constants['REL_STORE_DIR'];
        $this->constants['REL_COPY_DIR'] = 'copy' . DIRECTORY_SEPARATOR;
        $this->constants['COPY_DIR'] = $this->constants['CACHE_DIR'] . $this->constants['REL_COPY_DIR'];
        $this->constants['REL_UNIQUE_DIR'] = 'unique' . DIRECTORY_SEPARATOR;
        $this->constants['UNIQUE_DIR'] = $this->constants['CACHE_DIR'] . $this->constants['REL_UNIQUE_DIR'];
        $this->constants['LOG_PATH'] = $this->constants['SITE_ROOT'] . 'logs' . DIRECTORY_SEPARATOR;
        $this->constants['INFO_LOG'] = $this->constants['LOG_PATH'] . 'info.log';
        $this->constants['DEBUG_LOG'] = $this->constants['LOG_PATH'] . 'debug.log';
        $this->constants['ERROR_LOG'] = $this->constants['LOG_PATH'] . 'error.log';
        $this->constants['SQL_LOG'] = $this->constants['LOG_PATH'] . 'sql.log';
        $this->constants['ROUTES_JSON'] = $this->constants['RUNTIME_DIR'] . 'routes.json';

        $this->constants['FRAMEWORK_ROOT'] = $this->constants['EPHECT_ROOT'] . 'Framework' . DIRECTORY_SEPARATOR;
        $this->constants['HOOKS_ROOT'] = $this->constants['EPHECT_ROOT'] . 'Hooks' . DIRECTORY_SEPARATOR;
        $this->constants['PLUGINS_ROOT'] = $this->constants['EPHECT_ROOT'] . 'Plugins' . DIRECTORY_SEPARATOR;
        $this->constants['COMMANDS_ROOT'] = $this->constants['EPHECT_ROOT'] . 'Commands' . DIRECTORY_SEPARATOR;
        $this->constants['CUSTOM_COMMANDS_ROOT'] = $this->constants['SRC_ROOT'] . $this->constants['CONFIG_COMMANDS'] . DIRECTORY_SEPARATOR;
        $this->constants['CUSTOM_PAGES_ROOT'] = $this->constants['SRC_ROOT'] . $this->constants['CONFIG_PAGES'] . DIRECTORY_SEPARATOR;
        $this->constants['CUSTOM_COMPONENTS_ROOT'] = $this->constants['SRC_ROOT'] . $this->constants['CONFIG_COMPONENTS'] . DIRECTORY_SEPARATOR;

        $this->constants['CLASS_EXTENSION'] = '.class.php';
        $this->constants['HTML_EXTENSION'] = '.html';
        $this->constants['PREHTML_EXTENSION'] = '.phtml';
        $this->constants['CSS_EXTENSION'] = '.css';
        $this->constants['JS_EXTENSION'] = '.js';
        $this->constants['CLASS_JS_EXTENSION'] = '.class.js';
        $this->constants['MJS_EXTENSION'] = '.mjs';
        $this->constants['TPL_EXTENSION'] = '.tpl';
        $this->constants['TXT_EXTENSION'] = '.txt';

        $filename = $this->constants['EPHECT_ROOT'] . ($isWebApp ? 'web' : 'cli') . 'constants.php';

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

