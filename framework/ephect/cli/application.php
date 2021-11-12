<?php

namespace Ephect\CLI;

use Ephect\Commands\ApplicationCommands;
use Ephect\Commands\CommandRunner;
use Ephect\Core\AbstractApplication;

class Application extends AbstractApplication
{
    private $_phar = null;
    protected array $argv = [];
    protected int $argc = 0;

    public function getArgv(): array
    {
        return $this->argv;
    }

    public function getArgc(): int
    {
        return $this->argc;
    }

    public function init(): void
    {

    }

    public static function create(...$params): void
    {
        self::$instance = new Application();
        self::$instance->run(...$params);
    }

    public function run(...$params): void
    {
        $argv = $params[0];
        $argc = $params[1];

        $this->argv = $argv;
        $this->argc = $argc;

        $this->appDirectory = APP_CWD;
        
        $this->loadInFile();

        self::setExecutionMode(Application::PROD_MODE);
        self::useTransactions(true);

        $this->init();

        $this->execute();

    }

    protected function execute(): void
    {
        $commands = new ApplicationCommands($this);
        $runner = new CommandRunner($this, $commands);
        $runner->run();
        
    }

    public function displayConstants(): array
    {
        try {
            $constants = [];
            $constants['APP_NAME'] = APP_NAME;
            $constants['APP_CWD'] = APP_CWD;
            $constants['SCRIPT_ROOT'] = SCRIPT_ROOT;
            $constants['SRC_ROOT'] = SRC_ROOT;
            $constants['SITE_ROOT'] = SITE_ROOT;
            $constants['IS_PHAR_APP'] = IS_PHAR_APP ? 'TRUE' : 'FALSE';
            $constants['EPHECT_ROOT'] = EPHECT_ROOT;

            // $constants['EPHECT_VENDOR_SRC'] = EPHECT_VENDOR_SRC;
            // $constants['EPHECT_VENDOR_LIB'] = EPHECT_VENDOR_LIB;
            // $constants['EPHECT_VENDOR_APPS'] = EPHECT_VENDOR_APPS;

            if (APP_NAME !== 'egg') {
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

            Console::writeLine('Application constants are :');
            foreach ($constants as $key => $value) {
                Console::writeLine("\033[0m\033[0;36m" . $key . "\033[0m\033[0;33m" . ' => ' . "\033[0m\033[0;34m" . $value . "\033[0m\033[0m");
            }

            return $constants;
        } catch (\Throwable $ex) {
            Console::writeException($ex);

            return [];
        }
    }

}
