<?php

namespace Ephect\Commands\ShowConstants;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Core\ConstantsMaker;
use Throwable;

class Lib extends AbstractCommandLib
{
    public function displayConstants(): void
    {
        try {

            Console::writeLine('Application constants are :');

            $constantsMaker = new ConstantsMaker();
            $constantsMaker->log();

        } catch (Throwable $ex) {
            Console::error($ex);
        }
    }
}
