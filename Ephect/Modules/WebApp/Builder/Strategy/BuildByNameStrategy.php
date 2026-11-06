<?php

namespace Ephect\Modules\WebApp\Builder\Strategy;

use DateTime;
use Ephect\Framework\Utils\Text;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Registry\PluginRegistry;
use Ephect\Modules\Routing\Services\RouterService;
use Throwable;

class BuildByNameStrategy implements BuiderStrategyInterface
{
    public function build(array|string|null $routeItem): void
    {
        PluginRegistry::load();

        $page = $routeItem;
        if(is_array($routeItem)) {
            $page = (object)$routeItem->redirect;
        }

        Console::write("Compiling %s ... ", ConsoleColors::getColoredString($page, ConsoleColors::LIGHT_CYAN));
        Console::getLogger()->info("Compiling %s ... ", $page);

        $comp = new Component($page);
        $filename = $comp->getFlattenSourceFilename();

        $html = '';
        $error = '';

        try {
            $time_start = microtime(true);

            $functionArgs = $page === 'App' ? [] : RouterService::findRouteArguments($page);

            ob_start();
            $comp->render($functionArgs);
            $html = ob_get_clean();

            $duration = Text::durationFromToString($time_start);

            Console::writeLine("%s", ConsoleColors::getColoredString($duration . "ms", ConsoleColors::RED));
        } catch (Throwable $ex) {
            $error = Console::formatException($ex);
        }

        if ($error !== '') {
            Console::writeLine(
                "FATAL ERROR!%s %s",
                PHP_EOL,
                ConsoleColors::getColoredString(
                    $error,
                    ConsoleColors::WHITE,
                    ConsoleColors::BACKGROUND_RED
                )
            );
        }

        // File::safeWrite(\Constants::STATIC_DIR . $filename, $html);
    }
}
