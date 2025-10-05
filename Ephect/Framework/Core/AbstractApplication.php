<?php

namespace Ephect\Framework\Core;

use Ephect\Framework\Cache\Cache;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Element;
use Ephect\Framework\Registry\StateRegistry;
use Ephect\Plugins\Authentication\AuthenticationService;
use Throwable;

use function Ephect\Hooks\useMemory;

abstract class AbstractApplication extends Element
{
    use IniLoaderTrait;

    public const DEBUG_MODE = 'DEBUG';
    public const TEST_MODE = 'TEST';
    public const PROD_MODE = 'PROD';

    private static string $executionMode = self::PROD_MODE;
    private static bool $verboseMode = false;
    private static bool $useTransactions = true;

    protected array $commands = [];
    protected array $callbacks = [];
    protected string|null $appName = 'app';
    protected string $appTitle = '';
    protected string $scriptName = 'app.php';
    protected string $appDirectory = '';
    protected bool $canStop = false;
    protected string $dataConfName = '';
    private string $usage = '';
    private array $appini = [];

    public function __construct()
    {
        parent::__construct();
        useMemory(['buildDirectory' => \Constants::BUILD_DIR]);
    }

    public static function getExecutionMode(): string
    {
        return self::$executionMode;
    }

    public static function setExecutionMode($myExecutionMode): void
    {
        if (!$myExecutionMode) {
            $myExecutionMode = (\Constants::IS_WEB_APP) ? 'debug' : 'prod';
        }

        $prod = ($myExecutionMode == 'prod');
        $test = ($myExecutionMode == 'test' || $myExecutionMode == 'devel' || $myExecutionMode == 'dev');
        $debug = ($myExecutionMode == 'debug');

        if ($prod) {
            self::$executionMode = self::PROD_MODE;
        }
        if ($test) {
            self::$executionMode = self::TEST_MODE;
        }
        if ($debug) {
            self::$executionMode = self::DEBUG_MODE;
        }
    }

    public static function getVerboseMode(): bool
    {
        return self::$verboseMode;
    }

    public static function setVerboseMode($set = false): void
    {
        self::$verboseMode = $set;
    }

    public static function getTransactionUse(): bool
    {
        return self::$useTransactions;
    }

    public static function useTransactions($set = true): void
    {
        self::$useTransactions = $set;
    }

    public static function isProd(): bool
    {
        return self::$executionMode == self::PROD_MODE;
    }

    public static function isTest(): bool
    {
        return self::$executionMode == self::TEST_MODE;
    }

    public static function isDebug(): bool
    {
        return self::$executionMode == self::DEBUG_MODE;
    }

    public static function authenticateByToken($token): string
    {

        // On prend le token en cours
        if (is_string($token)) {
            // avec ce token on récupère l'utilisateur et un nouveau token
            $token = AuthenticationService::getPermissionByToken($token);
        }

        return $token;
    }

    abstract public function run(...$params): int;

    abstract public function displayConstants(): array;

    public function clearLogs(): string
    {
        $result = '';
        try {
            self::getLogger()->clearAll();

            $result = 'All logs cleared';
        } catch (Throwable $ex) {
            Console::error($ex);

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
        } catch (Throwable $ex) {
            Console::error($ex);

            $result = 'Impossible to delete runtime files';
        }
        return $result;
    }

    public function clearCache(): string
    {
        $result = '';
        try {
            Cache::clearCache();

            $result = 'All cache files deleted';
        } catch (Throwable $ex) {
            Console::error($ex);

            $result = 'Impossible to delete cache files';
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
            $exist = $this->loadINI(\Constants::CONFIG_DIR);
            if (!$exist) {
                return;
            }

            $this->appName = StateRegistry::readItem('application', 'name');
            $this->appTitle = StateRegistry::readItem('application', 'title');

        } catch (Throwable $ex) {
            Console::error($ex);
        }
    }

    public function help(): int
    {
        $help = '';
        Console::writeLine($this->getName());
        Console::writeLine('Expected commands : ');
        $usage = StateRegistry::item('commands');
        foreach ($usage as $long => $desc) {
            $help .= $desc;
        }
        Console::writeLine($help);

        return 0;
    }

    public function getName(): string|null
    {
        if (empty($this->appName) || $this->appName == 'app') {
            $this->appName = StateRegistry::ini('application', 'name');
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

    public function canStop(): bool
    {
        return $this->canStop;
    }

    public function getOS(): string
    {
        return PHP_OS;
    }

    abstract protected function execute(): int;

    //    protected function execute(): int {
    //        return 0;
    //    }
}
