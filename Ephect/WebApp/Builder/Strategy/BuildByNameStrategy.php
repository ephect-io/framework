<?php

namespace Ephect\WebApp\Builder\Strategy;

use DateTime;
use Ephect\Forms\Components\Component;
use Ephect\Forms\Registry\PluginRegistry;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Routing\RouterService;
use Throwable;

class BuildByNameStrategy implements BuiderStrategyInterface
{

    public function build(string $route): void
    {
        PluginRegistry::load();

        Console::write("Compiling %s ... ", ConsoleColors::getColoredString($route, ConsoleColors::LIGHT_CYAN));
        Console::getLogger()->info("Compiling %s ... ", $route);

        $comp = new Component($route);
        $filename = $comp->getFlattenSourceFilename();

        $html = '';
        $error = '';

        try {

            $time_start = microtime(true);

            $functionArgs = $route === 'App' ? [] : RouterService::findRouteArguments($route);

            ob_start();
            $comp->render($functionArgs);
            $html = ob_get_clean();

            $time_end = microtime(true);

            $duration = $time_end - $time_start;

            $utime = sprintf('%.3f', $duration);
            $raw_time = DateTime::createFromFormat('u.u', $utime);
            $duration = substr($raw_time->format('u'), 0, 3);

            Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
        } catch (Throwable $ex) {
            $error = Console::formatException($ex);
        }

        if ($error !== '') {
            Console::writeLine("FATAL ERROR!%s %s", PHP_EOL, ConsoleColors::getColoredString($error, ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
        }

        File::safeWrite(STATIC_DIR . $filename, $html);
    }
}