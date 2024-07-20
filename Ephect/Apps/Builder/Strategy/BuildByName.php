<?php

namespace Ephect\Apps\Builder\Strategy;

use DateTime;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Components\Component;
use Ephect\Framework\Registry\PluginRegistry;
use Ephect\Framework\Utils\File;
use Ephect\Plugins\Router\RouterService;
use Throwable;

class BuildByName
{

    public function do(string $name): string
    {
        PluginRegistry::load();

        Console::write("Compiling %s ... ", ConsoleColors::getColoredString($name, ConsoleColors::LIGHT_CYAN));
        Console::getLogger()->info("Compiling %s ... ", $name);

        $comp = new Component($name);
        $filename = $comp->getFlattenSourceFilename();

        $html = '';
        $error = '';

        try {

            $time_start = microtime(true);

            $functionArgs = $name === 'App' ? [] : RouterService::findRouteArguments($name);

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

        return $comp->getMotherUID();
    }
}