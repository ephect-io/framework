<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\CLI\Application;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Element;
use Exception;

class CommandRunner extends Element
{
    public function __construct(
        private readonly Application $_application,
        private readonly CommandCollectionInterface $_commands
    ) {
    }

    public function run(): int
    {
        $callback = null;

        $isFound = false;
        $commands = $this->_commands->commands();

        foreach ($commands as $command) {
            $struct = new CommandStructure($command);

            $callback = $struct->callback;

            $verb = $struct->verb;
            $subject = $struct->subject;

            $aac = $this->_application->getArgc();
            $aav = $this->_application->getArgv();

            $call = $subject != '' ? $verb . ':' . $subject : $verb;
            $commandLabel = !isset($aav[1]) ? '' : $aav[1];

            for ($i = 1; $i < $aac; $i++) {
                if ($aav[$i] == $call) {
                    $isFound = true;
                    break;
                }
            }

            if ($isFound) {
                $optionsStructure = $this->parseOptions($aav, $aac, $struct->shortArgs, $struct->longArgs);
                $this->_application->setCommandLineOptions($optionsStructure);
                break;
            }
        }


        $status = 1;
        if ($callback !== null && $isFound) {
            $status = $callback->run();
        }

        if (!$isFound) {
            Console::writeLine("A command labelled %s was not found", $commandLabel);
        }

        return $status;
    }

    private function parseOptions(array $args, int $count, array $shortOptions, array $longOptions): CommandOptionsStructure
    {
        $longArgs = [];
        $shortArgs = [];

        for ($i = 0; $i < $count; $i++) {
            $current = $args[$i];
            if (str_starts_with($current, '--')) { 
                if (str_contains($current, '=')) {
                    [$key, $value] = explode('=', substr($current, 2));

                    if (!in_array($key, $longOptions)) {
                        throw new Exception('Unknown parameter ' . $key . '!');
                    }
                    $longArgs[$key] = $value;
                } elseif (!str_contains($current, '=')) {
                    $key = substr($current, 2);

                    if (!in_array($key, $longOptions)) {
                        throw new Exception('Unknown parameter ' . $key . '!');
                    }
                    $longArgs[$key] = null;
                }
            } elseif (str_starts_with($current, '-')) {
                $next = $i + 1 < $count ? $args[$i + 1] : '';

                $key = substr($current, 1);

                if (!in_array($key, $shortOptions)) {
                    throw new Exception('Unknown parameter ' . $key . '!');
                }
                $value = str_starts_with($next, '-') ? null : ($next == '' ? null : $next);

                $shortArgs[$key] = $value;
            }
        }

        return new CommandOptionsStructure([
            'longArgs' => $longArgs,
            'shortArgs' => $shortArgs
        ]);
    }
}

