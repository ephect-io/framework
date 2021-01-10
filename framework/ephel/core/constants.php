<?php
/*
 * Copyright (C) 2019 David Blanchard
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Ephel\Core;

define('WEB_SEPARATOR', '/');
define('TMP_DIR', 'tmp');
define('APP_IS_PHAR', (\Phar::running() !== ''));
define('ROOT_PATH', 'phink');
// define('DOCUMENT_SCRIPT', $_SERVER['SCRIPT_FILENAME']);
define('BR', "<br />");

if (PHP_OS == 'WINNT') {
    $document_root = str_replace('\\\\', '\\', $document_root);
}

$document_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';

$script_path = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_DIRNAME);
if (substr($document_root, -4) !== 'web/' && ($p = strpos($script_path, 'src/web')) > -1) {
    $document_root = substr($script_path, 0, $p + 7);
}
define('DOCUMENT_ROOT', $document_root . DIRECTORY_SEPARATOR);
define('SRC_ROOT', substr(DOCUMENT_ROOT, 0, -4));
define('CONFIG_DIR', SRC_ROOT . 'config' . DIRECTORY_SEPARATOR);

define('SITE_ROOT', substr(SRC_ROOT, 0, -4));
define('APP_IS_WEB', (DOCUMENT_ROOT !== ''));

$vendor_dir = 'vendor' . DIRECTORY_SEPARATOR . 'phink' . DIRECTORY_SEPARATOR . 'phink' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR;
$portable_dir = 'framework' . DIRECTORY_SEPARATOR;
$lib = 'phink' . DIRECTORY_SEPARATOR . 'phink_library.php';

$framework_dir = $vendor_dir;
if (file_exists(SITE_ROOT . $portable_dir . $lib)) {
    $framework_dir = $portable_dir;
}

define('FRAMEWORK', $framework_dir);

$rewrite_base = '/';

if ($rewrite_base = file_get_contents(CONFIG_DIR . 'rewrite_base')) {
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

$appname = pathinfo(SITE_ROOT, PATHINFO_FILENAME);
define('APP_NAME', $appname);

// define('PHINK_VENDOR_SRC', 'vendor' . DIRECTORY_SEPARATOR . 'phink' . DIRECTORY_SEPARATOR . 'phink' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
// define('PHINK_VENDOR_SRC', 'framework' . DIRECTORY_SEPARATOR);
define('ROOT_NAMESPACE', 'Ephel');
define('PHINK_VENDOR_SRC', FRAMEWORK);
define('PHINK_VENDOR_LIB', PHINK_VENDOR_SRC . 'phink' . DIRECTORY_SEPARATOR);
define('PHINK_VENDOR_WIDGETS', PHINK_VENDOR_SRC . 'widgets' . DIRECTORY_SEPARATOR);
define('PHINK_VENDOR_PLUGINS', PHINK_VENDOR_SRC . 'plugins' . DIRECTORY_SEPARATOR);
define('PHINK_VENDOR_APPS', PHINK_VENDOR_SRC . 'apps' . DIRECTORY_SEPARATOR);
define('PHINKJS_VENDOR', PHINK_VENDOR_SRC . 'phinkjs' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'client' . DIRECTORY_SEPARATOR);
define('PHINK_ROOT', SITE_ROOT . PHINK_VENDOR_LIB);
define('PHINK_WIDGETS_ROOT', SITE_ROOT . PHINK_VENDOR_WIDGETS);
define('PHINK_PLUGINS_ROOT', SITE_ROOT . PHINK_VENDOR_PLUGINS);
define('PHINK_APPS_ROOT', SITE_ROOT . PHINK_VENDOR_APPS);
define('PHINKJS_ROOT', SITE_ROOT . PHINKJS_VENDOR);

define('APP_DIR', 'app' . DIRECTORY_SEPARATOR);

define('APP_ROOT', SRC_ROOT . APP_DIR);
define('CONTROLLER_ROOT', APP_ROOT . 'controllers' . DIRECTORY_SEPARATOR);
define('MODEL_ROOT', APP_ROOT . 'models' . DIRECTORY_SEPARATOR);
define('REST_ROOT', APP_ROOT . 'rest' . DIRECTORY_SEPARATOR);
define('VIEW_ROOT', APP_ROOT . 'views' . DIRECTORY_SEPARATOR);
define('BUSINESS_DIR', APP_DIR . 'business' . DIRECTORY_SEPARATOR);
define('BUSINESS_ROOT', SRC_ROOT . BUSINESS_DIR);

define('REL_RUNTIME_DIR', 'runtime' . DIRECTORY_SEPARATOR);
define('RUNTIME_DIR', SRC_ROOT . REL_RUNTIME_DIR);
define('REL_RUNTIME_JS_DIR', 'js' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR);
define('REL_RUNTIME_CSS_DIR', 'css' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR);
define('RUNTIME_JS_DIR', DOCUMENT_ROOT . REL_RUNTIME_JS_DIR);
define('RUNTIME_CSS_DIR', DOCUMENT_ROOT . REL_RUNTIME_CSS_DIR);
define('REL_CACHE_DIR', 'cache' . DIRECTORY_SEPARATOR);
define('CACHE_DIR', SRC_ROOT . REL_CACHE_DIR);

define('LOG_PATH', SRC_ROOT . 'logs' . DIRECTORY_SEPARATOR);

define('MAIN_VIEW', 'main');
define('LOGIN_VIEW', 'login');
define('MASTER_VIEW', 'master');
define('HOME_VIEW', 'home');
define('MAIN_PAGE', '/' . MAIN_VIEW . '.html');
define('LOGIN_PAGE', '/' . LOGIN_VIEW . '.html');
define('MASTER_PAGE', '/' . MASTER_VIEW . '.html');
define('HOME_PAGE', '/' . HOME_VIEW . '.html');
define('APP_DATA', SRC_ROOT . 'data' . DIRECTORY_SEPARATOR);
define('APP_BUSINESS', APP_ROOT . 'business' . DIRECTORY_SEPARATOR);
define('STARTER_FILE', 'starter.php');
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
define('DEFAULT_MODEL', ROOT_NAMESPACE . '\\MVC\\Model');
define('DEFAULT_CONTROLLER', ROOT_NAMESPACE . '\\MVC\\Controller');
define('DEFAULT_PARTIAL_CONTROLLER', ROOT_NAMESPACE . '\\MVC\\PartialController');
define('DEFAULT_CONTROL', ROOT_NAMESPACE . '\\Web\\UI\\Control');
define('DEFAULT_PARTIAL_CONTROL', ROOT_NAMESPACE . '\\Web\\UI\\PartialControl');

define('CONTROLLER', 'TController');
define('PARTIAL_CONTROLLER', 'TPartialController');
define('CONTROL', 'TControl');
define('PARTIAL_CONTROL', 'TPartialControl');
define('CLASS_EXTENSION', '.class.php');
define('HTML_EXTENSION', '.html');
define('PREHTML_EXTENSION', '.phtml');
define('JS_EXTENSION', '.js');
define('JSON_EXTENSION', '.json');
define('CSS_EXTENSION', '.css');
define('PHX_TERMINATOR', '<phx:eof />');
define('CREATIONS_PLACEHOLDER', '<phx:creationsPlaceHolder />');
define('ADDITIONS_PLACEHOLDER', '<phx:additionsPlaceHolder />');
define('AFTERBINDING_PLACEHOLDER', '<phx:afterBindingPlaceHolder />');
define('HTML_PLACEHOLDER', '<phx:htmlPlaceHolder />');
define('JS_PLACEHOLDER', '<phx:jsPlaceHolder />');
define('CSS_PLACEHOLDER', '<phx:cssPlaceHolder />');
define('ASP_OPEN_VAR', '<%');
define('ASP_CLOSE_VAR', '%>');
define('OPEN_VAR', '{{');
define('CLOSE_VAR', '}}');

/*
* define('CONTROL_ADDITIONS', PHP_EOL . "\public function createObjects() {" . PHP_EOL . CREATIONS_PLACEHOLDER . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL . "\public function declareObjects() {" . PHP_EOL . ADDITIONS_PLACEHOLDER . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL . "\public function afterBindingObjects() {" . PHP_EOL . AFTERBINDING_PLACEHOLDER . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL . "\public function displayHtml() {" . PHP_EOL . "?>" . PHP_EOL . HTML_PLACEHOLDER . PHP_EOL . "<?php" . PHP_EOL . "\t}" . PHP_EOL . '}' . PHP_EOL);
*/
define('CONTROL_ADDITIONS', PHP_EOL . "\public function createObjects() : void {" . PHP_EOL . CREATIONS_PLACEHOLDER . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL . "\public function declareObjects() : void {" . PHP_EOL . ADDITIONS_PLACEHOLDER . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL . "\public function displayHtml() : void {" . PHP_EOL . "?>" . PHP_EOL . HTML_PLACEHOLDER . PHP_EOL . "<?php" . PHP_EOL . "\t}" . PHP_EOL . '}' . PHP_EOL);
define('PHX_SQL_LIMIT', '<phx:sql_limit />');

define('RETURN_CODE', 1);
define('INCLUDE_FILE', 2);
define('REQUEST_TYPE_WEB', 'web');
define('REQUEST_TYPE_REST', 'rest');

define('DEBUG_LOG', LOG_PATH . 'debug.log');
define('ERROR_LOG', LOG_PATH . 'error.log');
define('SQL_LOG', LOG_PATH . 'sql.log');


unset($document_root);
unset($scheme);
unset($appname);
unset($rewrite_base);
