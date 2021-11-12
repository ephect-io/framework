<?php

namespace Ephect\Core;

use Ephect\Cache\Cache;
use Ephect\CLI\Console;
use Ephect\Element;
use Ephect\Registry\Registry;

abstract class AbstractApplication extends Element
{
    use IniLoaderTrait;

    const DEBUG_MODE = 'DEBUG';
    const TEST_MODE = 'TEST';
    const PROD_MODE = 'PROD';

    private static $_executionMode = self::PROD_MODE;
    private static $_verboseMode = false;
    private static $_useTransactions = true;

    protected $commands = [];
    protected $callbacks = [];
    protected $appName = 'app';
    protected $appTitle = '';
    protected $scriptName = 'app.php';
    protected $appDirectory = '';
    protected $canStop = false;
    protected $dataConfName = '';
    private $_usage = '';
    private $_appini = [];

    public function __construct()
    {
        parent::__construct();
    }

    abstract public function run(...$params) : void;

    protected function execute(): void
    {}

    abstract public function displayConstants(): array;

    public function clearLogs(): string
    {
        $result = '';
        try {
            self::getLogger()->clearAll();

            $result = 'All logs cleared';
        } catch (\Throwable $ex) {
            Console::writeException($ex);

            $result = 'Impossible to clear logs';
        }
        return $result;
    }

    public function clearRuntime(): string
    {
        $result = '';
        try {
            Cache::clearRuntime();

            $result = 'All runtime files deleted';
        } catch (\Throwable $ex) {
            Console::writeException($ex);

            $result = 'Impossible to delete runtime files';
        }
        return $result;
    }

    public function getDebugLog(): string
    {
        return self::getLogger()->getDebugLog();
    }

    public function getPhpErrorLog(): string
    {
        return self::getLogger()->getPhpErrorLog();
    }

    public function loadInFile(): void
    {
        try {
            $exist = $this->loadINI(CONFIG_DIR);
            if (!$exist) {
                return;
            }

            $this->appName = Registry::read('application', 'name');
            $this->appTitle = Registry::read('application', 'title');

        } catch (\Throwable $ex) {
            Console::writeException($ex);
        }
    }

    public function help(): void
    {
        $help = '';
        Console::writeLine($this->getName());
        Console::writeLine('Expected commands : ');
        $usage = Registry::item('commands');
        foreach($usage as $long => $desc) {
            $help .= $desc;
        }
        Console::writeLine($help);
    }

    public function getName(): string
    {
        if(empty($this->appName) || $this->appName == 'app') {
            $this->appName = Registry::ini('application', 'name');
        }

        return $this->appName;
    }

    public function getTitle(): string
    {
        return $this->appTitle;
    }

    public function getDirectory(): string
    {
        return $this->appDirectory;
    }


    public function canStop()
    {
        return $this->canStop;
    }

    public static function getExecutionMode(): string
    {
        return self::$_executionMode;
    }

    public function getOS(): string
    {
        return PHP_OS;
    }

    public static function setExecutionMode($myExecutionMode): void
    {
        if (!$myExecutionMode) {
            $myExecutionMode = (IS_WEB_APP) ? 'debug' : 'prod';
        }

        $prod = ($myExecutionMode == 'prod');
        $test = ($myExecutionMode == 'test' || $myExecutionMode == 'devel' || $myExecutionMode == 'dev');
        $debug = ($myExecutionMode == 'debug');

        if ($prod) {
            self::$_executionMode = self::PROD_MODE;
        }
        if ($test) {
            self::$_executionMode = self::TEST_MODE;
        }
        if ($debug) {
            self::$_executionMode = self::DEBUG_MODE;
        }
    }

    public static function getVerboseMode(): bool
    {
        return self::$_verboseMode;
    }

    public static function setVerboseMode($set = false)
    {
        self::$_verboseMode = $set;
    }

    public static function getTransactionUse(): bool
    {
        return self::$_useTransactions;
    }

    public static function useTransactions($set = true): void
    {
        self::$_useTransactions = $set;
    }

    public static function isProd(): bool
    {
        return self::$_executionMode == self::PROD_MODE;
    }

    public static function isTest(): bool
    {
        return self::$_executionMode == self::TEST_MODE;
    }

    public static function isDebug(): bool
    {
        return self::$_executionMode == self::DEBUG_MODE;
    }

    public static function authenticateByToken($token): string
    {

        // On prend le token en cours
        if (is_string($token)) {
            // avec ce token on récupère l'utilisateur et un nouveau token
            $token = TAuthentication::getUserCredentialsByToken($token);
        }

        return $token;
    }

}
