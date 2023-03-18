<?php

namespace Ephect\Framework\CLI;

use Ephect\Commands\Constants\Lib;
use Ephect\Framework\Commands\ApplicationCommands;
use Ephect\Framework\Commands\CommandRunner;
use Ephect\Framework\Core\AbstractApplication;

class Application extends AbstractApplication
{
    private $_phar = null;
    protected array $argv = [];
    protected int $argc = 0;

    /**
     * Get the CLI argv array
     *
     * @return array
     */
    public function getArgv(): array
    {
        return $this->argv;
    }

    /**
     * Get the CLI argc value
     *
     * @return integer
     */
    public function getArgc(): int
    {
        return $this->argc;
    }

    /**
     * Get CLI argument by index
     *
     * @param integer $index
     * @return null|string NULL when the index is not in $argc range
     */
    public function getArgi(int $index, string $default = ''): null|string
    {
        if($index > -1 && $this->argc > $index) {
            return $this->argv[$index];
        }

        return $default;
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
            $command = new Lib($this);
            $command->displayConstants();
        } catch (\Throwable $ex) {
            Console::error($ex);

            return [];
        }
    }

}
