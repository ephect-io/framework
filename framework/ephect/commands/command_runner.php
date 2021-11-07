<?php

namespace Ephect\Commands;

use Ephect\CLI\Application;
use Ephect\CLI\Console;
use Ephect\Element;

class CommandRunner extends Element
{

    public function __construct(
        private Application $_application,
        private CommandCollectionInterface $_commands
    ) {
    }

    public function run(): void
    {
        $isFound = false;
        $arguments = null;
        $callback = null;

        $isFound = false;
        $commands = $this->_commands->commands();

        foreach ($commands as $command) {
            $struct = new CommandStructure($command);

            $callback = $struct->callback;

            $verb = $struct->verb;
            $subject = $struct->subject;

            $call = $subject != '' ? $verb . ':' . $subject : $verb;

            $aac = $this->_application->getArgc();
            $aav = $this->_application->getArgv();

            for ($i = 1; $i < $aac; $i++) {
                if ($aav[$i] == $call) {
                    $isFound = true;

                    if (isset($aav[$i + 1])) {
                        if (substr($aav[$i + 1], 0, 1) !== '-') {
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

        if ($callback !== null && $isFound && $arguments === null) {
            $callback->run();
        } elseif ($callback !== null && $isFound && $arguments !== null) {
            call_user_func($callback, $arguments);
        }

        if ($isFound) return;

        Console::writeLine(<<<COWSAY
         ___________________________
        /       It looks like       \
        | you don't know what to do |
        \       Use egg --help       /
         ---------------------------
             \  ^__^
              \ (oo)\________
                (__)\        )\/\
                    ||----w |
                    ||     ||

        COWSAY);
    }
}
