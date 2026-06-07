<?php

namespace Ephect\Framework\CLI;

use Ephect\Framework\CLI\Enums\ConsoleOptionsEnum;
use Ephect\Framework\Commands\ApplicationCommands;
use Ephect\Framework\Commands\CommandOptionsStructure;
use Ephect\Framework\Commands\CommandRunner;
use Ephect\Framework\Core\AbstractApplication;
use Throwable;

class Application extends AbstractApplication
{
    protected array $argv = [];
    protected int $argc = 0;
    protected CommandOptionsStructure $commandLineOptions;
    private array $longArgs = [];
    private array $shortArgs = [];

    public static function create(mixed ...$params): int
    {
        self::$instance = new Application();
        return self::$instance->run(...$params);
    }

    public function run(mixed ...$params): int
    {
        $this->argv = $params[0];
        $this->argc = $params[1];

        $this->appDirectory = \Constants::APP_CWD;

        $this->loadInFile();

        self::setExecutionMode(Application::PROD_MODE);
        self::useTransactions(true);

        $this->init();

        return $this->execute();
    }

    public function init(): void
    {
    }

    protected function execute(): int
    {
        $commands = new ApplicationCommands($this);
        $runner = new CommandRunner($this, $commands);

        try {
            return $runner->run();
        } catch (Throwable $throwable) {
            Console::error($throwable);
            return 1;
        }
        
    }

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
     * @param string $default
     * @return null|string NULL when the index is not in $argc range
     */
    public function getArgi(int $index, string $default = ''): string
    {
        if ($index > -1 && $this->argc > $index) {
            return $this->argv[$index];
        }

        return $default;
    }

    public function setCommandLineOptions(CommandOptionsStructure $options): void
    {
        $this->longArgs = $options->longArgs;
        $this->shortArgs = $options->shortArgs;
    }

    public function getCommandLineOption(string $short, ?string $long = null): string|null
    {
        if(!$this->hasCommandLineOption($short, $long)) return null;
        
        return $this->longArgs[$long] ?? $this->shortArgs[$short];
    }

    public function hasCommandLineOption(string $short, ?string $long = null): bool
    {
        if (array_key_exists($short, $this->shortArgs) && array_key_exists($long, $this->longArgs)) {
            throw new \InvalidArgumentException("Both short and long options provided. Please provide only one of them.");
        }

        return array_key_exists($short, $this->shortArgs) 
        || ($long !== null && array_key_exists($long, $this->longArgs));
    }

    public function displayConstants(): array
    {
        return [
        ];
    }
}
