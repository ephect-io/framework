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
        $result = null;
        $callback = null;

        $isFound = false;
        $commands = $this->_commands->commands();

        foreach ($commands as $command) {
            $struct = new CommandStructure($command);

            $callback = $struct->callback;

            $short = $struct->short;
            $long = $struct->long;

            $aac = $this->_application->getArgc();
            $aav = $this->_application->getArgv();

            for ($i = 1; $i < $aac; $i++) {
                if ($aav[$i] == '--' . $long) {
                    $isFound = true;

                    if (isset($aav[$i + 1])) {
                        if (substr($aav[$i + 1], 0, 1) !== '-') {
                            $result = $aav[$i + 1];
                        }
                    }

                    break;
                }

                if ($aav[$i] == '-' . $short) {
                    $isFound = true;

                    $sa = explode('=', $aav[$i]);
                    if (count($sa) > 1) {
                        $result = $sa[1];
                    }

                    break;
                }
            }
            if ($isFound) {
                break;
            }
        }

        if ($callback !== null && $isFound && $result === null) {
            call_user_func($callback);
        } elseif ($callback !== null && $isFound && $result !== null) {
            call_user_func($callback, $result);
        }

        $isFound = false;
        $commands = $this->_commands->commands();

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
