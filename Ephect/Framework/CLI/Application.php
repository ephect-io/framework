<?php

namespace Ephect\Framework\CLI;

use Ephect\Commands\Constants\Lib;
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
        return $runner->run();
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

    public function getCommandLineLongOptions(string|null $key = null): array|string|null
    {
        if ($key !== null) {
            return $this->longArgs[$key] ?? null;
        }
        return $this->longArgs;
    }

    public function hasCommandLineLongOption(string $key): bool
    {
        return array_key_exists($key, $this->longArgs);
    }

    public function getCommandLineShortOptions(string|null $key = null): array|string|null
    {
        if ($key !== null) {
            return $this->shortArgs[$key] ?? null;
        }
        return $this->shortArgs;
    }

    public function hasCommandLineShortOption(string $key): bool
    {
        return array_key_exists($key, $this->shortArgs);
    }

    public function displayConstants(): array
    {
        return [
        ];
    }
}
