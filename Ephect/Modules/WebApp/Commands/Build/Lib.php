<?php

namespace Ephect\Modules\WebApp\Commands\Build;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Utils\Text;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Modules\WebApp\Builder\Builder;
use Ephect\Modules\WebApp\Services\BuildService;

class Lib extends AbstractCommandLib
{
    private Builder $builder;

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function clear(): void
    {
        $application = $this->parent;

        Console::write("%s ... ", ConsoleColors::getColoredString("Clearing runtime and logs", ConsoleColors::LIGHT_CYAN));

        $application->clearRuntime();
        $application->clearLogs();

        Console::writeLine("%s", ConsoleColors::getColoredString("Done", ConsoleColors::GREEN));
    }

    public function register(): void
    {
        Console::write("%s ... ", ConsoleColors::getColoredString("Registering pages and components", ConsoleColors::LIGHT_CYAN));

        $time_start = microtime(true);

        $service = new BuildService();
        $this->builder = $service->build();

        $duration = Text::durationFromToString($time_start);

        Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
    }
        
    public function build(): void
    {
        Console::write("%s ... ", ConsoleColors::getColoredString("Building pages and components", ConsoleColors::LIGHT_CYAN));

        $time_start = microtime(true);

        $this->builder->buildAllRoutes();

        $duration = Text::durationFromToString($time_start);

        Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
    }
}
