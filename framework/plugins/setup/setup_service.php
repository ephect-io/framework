<?php

namespace Ephect\Plugins\Setup;

use Ephect\Core\PhpInfo;
use Ephect\IO\Utils;
use Ephect\Logger\Logger;
use Ephect\Web\Curl;

class SetupService
{
    private $_rewriteBase = '/';

    public static function create(): SetupService
    {
        return new SetupService();
    }

    public function __construct()
    {
        $rewriteBase = dirname(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME)) . DIRECTORY_SEPARATOR;
        $rewriteBase = str_replace("//", "/", $rewriteBase);
        define('SETUP_REWRITE_BASE', $rewriteBase);
    }

    public function getRewriteBase(): string
    {
        return SETUP_REWRITE_BASE;
    }

    public function installPhinkJS(): bool
    {
        $ok = false;

        try {

            $filename = 'phinkjs.tar.gz';
            $tarfilename = 'phinkjs.tar';
            $phinkjs_dirname = 'phinkjs' . DIRECTORY_SEPARATOR;

            $filepath = SETUP_SITE_ROOT . SETUP_FRAMEWORK;

            if (file_exists($filepath . $phinkjs_dirname)) {
                chdir($filepath . $phinkjs_dirname);

                unlink('.gitignore');
                $github = '.github' . DIRECTORY_SEPARATOR;
                if (file_exists($filepath . $phinkjs_dirname . $github)) {
                    Utils::delTree($filepath . $phinkjs_dirname . $github);
                }

                chdir('..');
                FileUtils::delTree($filepath . $phinkjs_dirname);
            }

            if (!file_exists($filepath . $phinkjs_dirname)) {
                chdir($filepath);
            }

            if (file_exists($tarfilename)) {
                unlink($tarfilename);
            }

            $curl = new Curl();
            $result = $curl->request('https://github.com/CodePhoenixOrg/PhinkJS/archive/master.tar.gz');
            $ok = false !== file_put_contents($filename, $result->content);

            $p = new \PharData($filename);
            $p->decompress();

            if (file_exists($filename)) {
                unlink($filename);
            }

            $phar = new \PharData($tarfilename);
            $phar->extractTo($filepath);

            if (file_exists($tarfilename)) {
                unlink($tarfilename);
            }


            $ok = $ok && rename('PhinkJS-master', 'phinkjs');
            chmod('phinkjs', 0775);

        } catch (\Exception $ex) {
            $ok = false;
            $log = Logger::create();
            $log->error($ex);
        }

        return $ok;
    }

    public function fixRewritBase(): ?string
    {
        $result = null;
        $ok = false;

        if ($ok = file_exists('bootstrap.php')) {

            $ok = $ok && false !== file_put_contents(SETUP_CONFIG_DIR . 'rewrite_base', SETUP_REWRITE_BASE);

            if (file_exists('.htaccess') && ($htaccess = file_get_contents('.htaccess'))) {
                $htaccess = str_replace(PHP_EOL, ';', $htaccess);
                $text = strtolower($htaccess);

                $ps = strpos($text, 'rewritebase');
                if ($ps > -1) {
                    $pe = strpos($htaccess, ' ', $ps);
                    $rewriteBaseKey = substr($htaccess, $ps, $pe - $ps);
                    $pe = strpos($htaccess, ';', $ps);
                    $rewriteBaseEntry = substr($htaccess, $ps, $pe - $ps);

                    $htaccess = str_replace($rewriteBaseEntry, $rewriteBaseKey . ' ' . SETUP_REWRITE_BASE, $htaccess);
                    $htaccess = str_replace(';', PHP_EOL, $htaccess);

                    $ok = $ok && false !== file_put_contents('.htaccess', $htaccess);
                }
            }
        }

        $result = ($ok) ? SETUP_REWRITE_BASE : null;

        return $result;
    }

    public function findFramework(): bool
    {
        $ok = false;

        $vendor_dir = 'vendor' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR;
        $portable_dir = 'framework' . DIRECTORY_SEPARATOR;
        $lib = 'ephect' . DIRECTORY_SEPARATOR . 'phink_library.php';

        $framework_dir = '';
        if (file_exists(SETUP_SITE_ROOT . $vendor_dir . $lib)) {
            $framework_dir = $vendor_dir;
        }

        if (file_exists(SETUP_SITE_ROOT . $portable_dir . $lib)) {
            $framework_dir = $portable_dir;
        }
        $ok = false !== file_put_contents(SETUP_CONFIG_DIR . 'framework', $framework_dir);

        return $ok;
    }

    public function makeBootstrap(): bool
    {

        $ok = true;

        $bootstrap1 = <<<BOOTSTRAP1
<?php
\$is127 = (((\$host = array_shift(\$hostPort = explode(':', \$_SERVER['HTTP_HOST']))) . (isset(\$hostPort[1]) ? \$port = ':' . \$hostPort[1] : \$port = '') == '127.0.0.1' . \$port) ? \$hostname = 'localhost' : \$hostname = \$host) !== \$host;
\$isIndex = (((strpos(\$_SERVER['REQUEST_URI'], 'index.php')  > -1) ? \$requestUri = str_replace('index.php', '', \$_SERVER['REQUEST_URI']) : \$requestUri = \$_SERVER['REQUEST_URI']) !== \$_SERVER['REQUEST_URI']);

if(\$is127 || \$isIndex) {
    header('Location: //' . \$hostname . \$port . \$requestUri);
    exit(302);
}
define('CONFIG_DIR', '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
define('FRAMEWORK', trim(file_get_contents(CONFIG_DIR . 'framework')));
include '../../framework/bootstrap.php';

BOOTSTRAP1;

        $bootstrap2 = <<<BOOTSTRAP2
<?php
define('CONFIG_DIR', '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
define('FRAMEWORK', trim(file_get_contents(CONFIG_DIR . 'framework')));
include '../../framework/bootstrap.php';

BOOTSTRAP2;

        // include '../../vendor/autoload.php';

        $serverApi = strtolower(PhpInfo::getGeneralSection()->server_api);

        if (strpos($serverApi, 'embedded') > -1 || strpos($serverApi, 'built-in') > -1) {
            $ok = false !== file_put_contents('bootstrap.php', $bootstrap1);
        } else {
            $ok = false !== file_put_contents('bootstrap.php', $bootstrap2);
        }

        return $ok;
    }

    public function makeIndex(): bool
    {
        $index = <<<INDEX
<?php
include 'bootstrap.php';

Ephect\Web\Application::create();

INDEX;

        return false !== file_put_contents('index.php', $index);
    }
}
