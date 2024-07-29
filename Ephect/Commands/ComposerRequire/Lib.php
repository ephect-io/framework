<?php

namespace Ephect\Commands\ComposerRequire;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;

class Lib extends AbstractCommandLib
{
    public function require(string $package): void
    {
        exec("composer require {$package}", $output, $returnCode);
        if ($returnCode !== 0) {
            foreach ($output as $item) {
                Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColors::RED, ConsoleColors::WHITE));
            }
        }
    }

}

