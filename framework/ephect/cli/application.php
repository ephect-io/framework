<?php

namespace Ephect\CLI;

use Ephect\Core\AbstractApplication;
use Ephect\IO\Utils;
use Ephect\Utils\Zip;
use Ephect\Web\Curl;

class Application extends AbstractApplication
{
    protected $_argv;
    protected $_argc;
    private $_phar = null;

    public function __construct(array $argv = [], int $argc = 0, string $appDirectory = '.')
    {
        parent::__construct();

        $this->_argv = $argv;
        $this->_argc = $argc;

        $this->scriptName = $argv[0];

        $this->appDirectory = $appDirectory . DIRECTORY_SEPARATOR;

        $path = explode(DIRECTORY_SEPARATOR, $this->appDirectory);
        $scriptDir = $this->appDirectory . '..' . DIRECTORY_SEPARATOR;
        $srcDir = $scriptDir . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $srcDir = realpath($srcDir) . DIRECTORY_SEPARATOR;
        $siteDir = $srcDir . '..' . DIRECTORY_SEPARATOR;
        $siteDir = realpath($siteDir) . DIRECTORY_SEPARATOR;
        $scriptDir = realpath($scriptDir) . DIRECTORY_SEPARATOR;

        array_pop($path);
        array_pop($path);
        $this->appName = array_pop($path);

        if (IS_PHAR_APP) {
            $this->appName = pathinfo($argv[0])['filename'];
            $this->appDirectory = str_replace('phar://', '', $scriptDir);
        }

        $vendor_dir = 'vendor' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR;
        $portable_dir = 'framework' . DIRECTORY_SEPARATOR;
        $lib = 'ephect' . DIRECTORY_SEPARATOR . 'ephect_library.php';

        $framework_dir = $vendor_dir;

        // define('APP_NAME', $this->appName);

        $script_root = '.' . $scriptDir;
        $site_root = $script_root;
        $ephect_root = \Phar::running();

        if (!IS_PHAR_APP) {
            $script_root = $scriptDir;

            if (APP_NAME == 'egg') {
                $site_root = $scriptDir;
                if (file_exists($site_root . $portable_dir . $lib)) {
                    $framework_dir = $portable_dir;
                }
                $ephect_vendor_lib = $framework_dir . 'ephect' . DIRECTORY_SEPARATOR;
                $ephect_vendor_apps = $framework_dir . 'apps' . DIRECTORY_SEPARATOR;

                $ephect_root = @realpath($site_root . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ephect') . DIRECTORY_SEPARATOR;
            } else {
                $site_root =  $siteDir;
                if (file_exists($site_root . $portable_dir . $lib)) {
                    $framework_dir = $portable_dir;
                }
                $ephect_vendor_lib = $framework_dir . 'ephect' . DIRECTORY_SEPARATOR;
                $ephect_vendor_apps = $framework_dir . 'apps' . DIRECTORY_SEPARATOR;

                $ephect_root = $site_root . $ephect_vendor_lib;
            }
        }

        // define('EPHECT_VENDOR_SRC', $framework_dir);
        // define('EPHECT_VENDOR_LIB', $ephect_vendor_lib);
        // define('EPHECT_VENDOR_APPS', $ephect_vendor_apps);

        define('SCRIPT_ROOT', $script_root);
        // define('SITE_ROOT', $site_root);
        // define('EPHECT_ROOT', $ephect_root);

        // define('EPHECT_APPS_ROOT', SITE_ROOT . EPHECT_VENDOR_APPS);
        // define('SRC_ROOT', SITE_ROOT . 'src' . DIRECTORY_SEPARATOR);

        // define('APP_ROOT', SRC_ROOT . 'app' . DIRECTORY_SEPARATOR);
        define('APP_SCRIPTS', APP_ROOT . 'scripts' . DIRECTORY_SEPARATOR);
        define('APP_DATA', SRC_ROOT . 'data' . DIRECTORY_SEPARATOR);
        define('APP_BUSINESS', APP_ROOT . 'business' . DIRECTORY_SEPARATOR);
        define('CONTROLLER_ROOT', APP_ROOT . 'controllers' . DIRECTORY_SEPARATOR);
        define('BUSINESS_ROOT', APP_ROOT . 'business' . DIRECTORY_SEPARATOR);
        define('MODEL_ROOT', APP_ROOT . 'models' . DIRECTORY_SEPARATOR);
        define('REST_ROOT', APP_ROOT . 'rest' . DIRECTORY_SEPARATOR);
        define('VIEW_ROOT', APP_ROOT . 'views' . DIRECTORY_SEPARATOR);
        // define('CACHE_DIR', SRC_ROOT . 'cache' . DIRECTORY_SEPARATOR);

        $useTransaction = $this->setCommand('useTransactions');
        $execution = $this->setCommand('executionMode');
        $verbose = $this->setCommand('verbose');

        self::setExecutionMode($execution);
        self::useTransactions($useTransaction);

        $this->ignite();
        $this->execute();

        $this->init();
        $this->run();
    }

    public function init(): void
    {
    }

    public static function create(...$params): void
    {
        self::$instance = new Application();
        self::$instance->run($params);
    }

    public function run(?array ...$params): void
    {
    }

    protected function ignite(): void
    {
        parent::ignite();

        if (!IS_PHAR_APP) {
            $this->setCommand(
                'make-master-phar',
                '',
                'Make a phar archive of the current application with files from the master repository.',
                function () {
                    $this->makeMasterPhar();
                }
            );
            $this->setCommand(
                'make-phar',
                '',
                'Make a phar archive of the current application with files in vendor directory.',
                function () {
                    $this->makeVendorPhar();
                }
            );
            $this->setCommand(
                'require-master',
                '',
                'Download the ZIP file of the master branch of ephect framework.',
                function () {
                    $this->_requireMaster();
                }
            );
        }
        $this->setCommand(
            'running',
            '',
            'Show Phar::running() output',
            function () {
                self::writeLine(\Phar::running());
            }
        );

        $this->setCommand(
            'source-path',
            '',
            'Display the running application source directory.',
            function () {
                $this->writeLine($this->getDirectory());
            }
        );

        $this->setCommand(
            'script-path',
            '',
            'Display the running application root.',
            function () {
                $this->writeLine(SCRIPT_ROOT);
            }
        );

        $this->setCommand(
            'display-tree',
            '',
            'Display the tree of the current application.',
            function () {
                $this->displayTree($this->appDirectory);
            }
        );

        $this->setCommand(
            'display-ephect-tree',
            '',
            'Display the tree of the ephect framework.',
            function () {
                $this->displayephectTree();
            }
        );

        $this->setCommand(
            'display-master-tree',
            '',
            'Display the tree of the master branch of ephect framework previously downloaded.',
            function () {
                try {
                    $this->displayTree('master' . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ephect');
                } catch (\Throwable $ex) {
                    $this->writeException($ex);
                }
            }
        );

        $this->setCommand(
            'show-arguments',
            '',
            'Show the application arguments.',
            function (callable $callback = null) {
                $data = ['argv' => $this->_argv, 'argc' => $this->_argc];
                $this->writeLine($data);

                if ($callback !== null) {
                    \call_user_func($callback, $data);
                }
            }
        );

        $this->setCommand(
            'display-history',
            '',
            'Display commands history.',
            function () {
                try {
                    $this->writeLine(readline_list_history());
                } catch (\Throwable $ex) {
                    $this->writeException($ex);
                }
            }
        );
    }

    protected function execute(): void
    {
        $isFound = false;
        $result = null;
        $callback = null;

        foreach ($this->commands as $long => $cmd) {
            $short = $cmd['short'];
            $callback = $cmd['callback'];
            for ($i = 1; $i < $this->_argc; $i++) {
                if ($this->_argv[$i] == '--' . $long) {
                    $isFound = true;

                    if (isset($this->_argv[$i + 1])) {
                        if (substr($this->_argv[$i + 1], 0, 1) !== '-') {
                            $result = $this->_argv[$i + 1];
                        }
                    }

                    break;
                }

                if ($this->_argv[$i] == '-' . $short) {
                    $isFound = true;

                    $sa = explode('=', $this->_argv[$i]);
                    if (count($sa) > 1) {
                        $result = $sa[1];
                    }

                    break;
                }
            }
            if ($isFound) {
                break;
            }
        }

        if ($callback !== null && $isFound && $result === null) {
            call_user_func($callback);
        } elseif ($callback !== null && $isFound && $result !== null) {
            call_user_func($callback, $result);
        }
    }

    protected function displayConstants(): array
    {
        try {
            $constants = [];
            $constants['APP_NAME'] = APP_NAME;
            $constants['SCRIPT_ROOT'] = SCRIPT_ROOT;
            $constants['SITE_ROOT'] = SITE_ROOT;
            $constants['IS_PHAR_APP'] = IS_PHAR_APP ? 'TRUE' : 'FALSE';
            $constants['EPHECT_ROOT'] = EPHECT_ROOT;

            // $constants['EPHECT_VENDOR_SRC'] = EPHECT_VENDOR_SRC;
            // $constants['EPHECT_VENDOR_LIB'] = EPHECT_VENDOR_LIB;
            // $constants['EPHECT_VENDOR_APPS'] = EPHECT_VENDOR_APPS;

            if (APP_NAME !== 'egg') {
                $constants['SRC_ROOT'] = SRC_ROOT;
                $constants['APP_ROOT'] = APP_ROOT;
                $constants['APP_SCRIPTS'] = APP_SCRIPTS;
                $constants['APP_BUSINESS'] = APP_BUSINESS;
                $constants['MODEL_ROOT'] = MODEL_ROOT;
                $constants['VIEW_ROOT'] = VIEW_ROOT;
                $constants['CONTROLLER_ROOT'] = CONTROLLER_ROOT;
                $constants['REST_ROOT'] = REST_ROOT;
                $constants['APP_DATA'] = APP_DATA;
                $constants['CACHE_DIR'] = CACHE_DIR;
            }
            $constants['LOG_PATH'] = LOG_PATH;
            $constants['DEBUG_LOG'] = DEBUG_LOG;
            $constants['ERROR_LOG'] = ERROR_LOG;

            $this->writeLine('Application constants are :');
            foreach ($constants as $key => $value) {
                $this->writeLine("\033[0m\033[0;36m" . $key . "\033[0m\033[0;33m" . ' => ' . "\033[0m\033[0;34m" . $value . "\033[0m\033[0m");
            }

            return $constants;
        } catch (\Throwable $ex) {
            $this->writeException($ex);

            return [];
        }
    }

    private function _requireMaster(): object
    {
        $result = [];

        $libRoot = $this->appDirectory . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;

        if (!file_exists($libRoot)) {
            mkdir($libRoot);
        }

        $master = $libRoot . 'master';
        $filename = $master . '.zip';
        $ephectDir = $master . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR;

        $tree = [];

        if (!file_exists($filename)) {
            self::writeLine('Downloading ephect github master');
            $curl = new Curl();
            $result = $curl->request('https://codeload.github.com/CodePhoenixOrg/ephect/zip/master');
            file_put_contents($filename, $result->content);
        }

        if (file_exists($filename)) {
            self::writeLine('Inflating ephect master archive');
            $zip = new Zip();
            $zip->inflate($filename);
        }

        if (file_exists($master)) {
            $tree = Utils::walkTree($ephectDir, ['php']);
        }

        $result = ['path' => $ephectDir, 'tree' => $tree];

        return (object) $result;
    }

    private function _requireVendor(string $path = ''): object
    {
        $result = [];

        $tree = Utils::walkTree(EPHECT_ROOT, ['php']);

        $result = ['path' => EPHECT_ROOT, 'tree' => $tree];

        return (object) $result;
    }

    public function addFileToPhar($file, $name): void
    {
        $this->writeLine("Adding %s as %s", $file, $name);
        $this->_phar->addFile($file, $name);
    }

    public function makeMasterPhar(): void
    {
        $ephectTree = $this->_requireMaster();
        $this->_makePhar($ephectTree);
    }

    public function makeVendorPhar(): void
    {
        $ephectTree = $this->_requireVendor();
        $this->_makePhar($ephectTree);
    }

    private function _makePhar(object $ephectTree): void
    {
        try {

            // if (IS_WEB_APP) {
            //     throw new \Exception('Still cannot make a phar of a web application!');
            // }
            ini_set('phar.readonly', 0);

            // the current directory must be src
            $pharName = $this->appName . ".phar";
            $buildRoot = $this->appDirectory . '..' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR;

            if (file_exists($buildRoot . $pharName)) {
                unlink($buildRoot . $pharName);
            }

            if (!file_exists($buildRoot)) {
                mkdir($buildRoot);
            }

            $this->_phar = new \Phar(
                $buildRoot . $pharName,
                \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_FILENAME,
                $pharName
            );
            // start buffering. Mandatory to modify stub.
            $this->_phar->startBuffering();

            // Get the default stub. You can create your own if you have specific needs
            $defaultStub = $this->_phar->createDefaultStub("app.php");

            $this->writeLine('APP_DIR::' . $this->appDirectory);
            $this->addPharFiles();

            $ephectDir = $ephectTree->path;
            $ephect_builder = $ephectDir . 'ephect_library.php';

            $ephect_builder = Utils::reducePath($ephect_builder);
            $this->addFileToPhar($ephect_builder, "ephect_library.php");

            foreach ($ephectTree->tree as $file) {
                $filename = $ephectTree->path . $file;
                $filename = realpath($filename);
                $info = pathinfo($file, PATHINFO_BASENAME);

                $this->addFileToPhar($filename, $info);
            }

            // Create a custom stub to add the shebang
            $execHeader = "#!/usr/bin/env php \n";
            if (PHP_OS == 'WINNT') {
                $execHeader = "@echo off\r\nphp.exe\r\n";
            }
            $stub = $execHeader . $defaultStub;

            // Add the stub
            $this->_phar->setStub($stub);

            $this->_phar->stopBuffering();

            $buildRoot = $this->appDirectory . '..' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR;
            $execname = $buildRoot . $this->appName;
            if (PHP_OS == 'WINNT') {
                $execname .= '.bat';
            }

            rename($buildRoot . $this->appName . '.phar', $execname);
            chmod($execname, 0755);
        } catch (\Throwable $ex) {
            $this->writeException($ex);
        }
    }

    public function addPharFiles(): void
    {
        try {
            $tree = Utils::walkTree($this->appDirectory, ['php']);

            if (isset($tree[$this->appDirectory . $this->scriptName])) {
                unset($tree[$this->appDirectory . $this->scriptName]);
                $this->addFileToPhar($this->appDirectory . $this->scriptName, $this->scriptName);
            }
            foreach ($tree as $filename) {
                $this->addFileToPhar($this->appDirectory . $filename, $filename);
            }
        } catch (\Throwable $ex) {
            $this->writeException($ex);
        }
    }

    public function displayephectTree(): void
    {
        // $tree = [];
        // \ephect\Utils\TFileUtils::walkTree(EPHECT_ROOT, $tree);
        $tree = Utils::walkTree(EPHECT_ROOT);

        $this->writeLine($tree);
    }

    public function displayTree($path): void
    {
        $tree = Utils::walkTree($path);
        $this->writeLine($tree);
    }

    public static function write($string, ...$params): void
    {
        $result = self::_write($string, $params);
        if (!IS_WEB_APP) {
            print $result;
        } else {
            self::getLogger()->debug($result);
        }
    }

    public static function writeLine($string, ...$params): void
    {
        $result = self::_write($string, $params);
        if (!IS_WEB_APP) {
            print $result . PHP_EOL;
        } else {
            self::getLogger()->debug($result . PHP_EOL);
        }
    }

    public static function readLine(?string $prompt = null): string
    {
        $result = '';

        $result = readline($prompt);
        readline_add_history($result);

        return $result;
    }

    public static function writeException(\Throwable $ex, $file = null, $line = null): void
    {
        if (!IS_WEB_APP) {
            $message = '';

            if ($ex instanceof \ErrorException) {
                $message .= 'Error severity: ' . $ex->getSeverity() . PHP_EOL;
            }
            $message .= 'Error code: ' . $ex->getCode() . PHP_EOL;
            $message .= 'In ' . $ex->getFile() . ', line ' . $ex->getLine() . PHP_EOL;
            $message .= 'With the message: ' . $ex->getMessage() . PHP_EOL;
            $message .= 'Stack trace: ' . $ex->getTraceAsString() . PHP_EOL;

            print "\033[41m\033[1;37m" . $message . "\033[0m\033[0m";
        } else {
            self::getLogger()->error($ex, $file, $line);
        }
    }
}
