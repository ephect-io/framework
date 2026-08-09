<?php

namespace Ephect\Modules\ParallelBridge\WebApp\Builder\Strategy;

use parallel\Channel;
use DateTime;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Utils\File;
use Ephect\Modules\Forms\Components\Component;
use Ephect\Modules\Forms\Registry\PluginRegistry;
use Ephect\Modules\ParallelBridge\Parallel\Task;
use Ephect\Modules\ParallelBridge\Parallel\TaskStructure;
use Ephect\Modules\ParallelBridge\Runner\TaskRunner;
use Ephect\Modules\Routing\Services\RouterService;
use Ephect\Modules\WebApp\Builder\Strategy\BuilderStrategyInterface;
use Throwable;

class BuildByTaskStrategy implements BuilderStrategyInterface
{
    public function build(string $route): void
    {
        if(!class_exists('parallel')) {
            return;
        }
        $struct = new TaskStructure(['name' => $route, 'arguments' => [$route]]);
        $task = new Task($struct);
        $task->setCallback(function (string $route, string $framework_root, Channel $channel) {

            include $framework_root . 'bootstrap.php';

            PluginRegistry::load();

            Console::write("Compiling %s ... ", ConsoleColors::getColoredString($route, ConsoleColors::LIGHT_CYAN));
            Console::getLogger()->info("Compiling %s ...", $route);

            $comp = new Component($route);
            $filename = $comp->getFlattenSourceFilename();

            $html = '';
            $error = '';

            try {

                $time_start = microtime(true);

                ob_start();

                $functionArgs = RouterService::findRouteArguments($route);

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

            $channel->send(['name' => $route, 'filename' => $filename, 'html' => $html, 'error' => $error]);
        });

        $runner = new TaskRunner($task);
        $runner->run();

        $result = $runner->getResult();

        $runner->close();

        $filename = $result['filename'];
        $html = $result['html'];
        $error = $result['error'];

        if ($error !== '') {
            Console::writeLine("FATAL ERROR!%s %s", PHP_EOL, ConsoleColors::getColoredString($error, ConsoleColors::WHITE, ConsoleColors::BACKGROUND_RED));
        }

        File::safeWrite(\Constants::STATIC_DIR . $filename, $html);
    }
}
