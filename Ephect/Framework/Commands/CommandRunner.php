<?php

namespace Ephect\Framework\Commands;

use Ephect\Framework\CLI\Application;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Element;

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

            $call = $subject != '' ? $verb . ':' . $subject : $verb;
            $commandLabel = !isset($aav[1]) ? '' : $aav[1];

            $aac = $this->_application->getArgc();
            $aav = $this->_application->getArgv();

            for ($i = 1; $i < $aac; $i++) {
                if ($aav[$i] == $call) {
                    $isFound = true;

                    if (isset($aav[$i + 1])) {
                        if (!str_starts_with($aav[$i + 1], '-')) {
                            $arguments = $aav[$i + 1];
                        }
                    }

                    break;
                }
            }

            if ($isFound) {
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
}
