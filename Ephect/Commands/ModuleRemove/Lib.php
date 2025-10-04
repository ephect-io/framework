<?php

namespace Ephect\Commands\ModuleRemove;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;

class Lib extends AbstractCommandLib
{
    public function remove(string $package, string $version): void
    {

        $binScript = \Constants::SITE_ROOT . "vendor/bin/" . str_replace('/', '_', $package) . '_install.sh';
        if (PHP_OS == 'WINNT') {
            $binScript = \Constants::SITE_ROOT . "vendor\\bin\\" . str_replace('/', '_', $package) . '_install.bat';
        }

        if (file_exists($binScript)) {
            Console::writeLine(ConsoleColors::getColoredString("An remove script was found", ConsoleColors::BLUE, ConsoleColors::WHITE));
            if (Console::readYesOrNo("Do you want to run the script?")) {
                exec("$binScript -r", $output, $returnCode);
                if ($returnCode !== 0) {
                    foreach ($output as $item) {
                        Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColors::RED, ConsoleColors::WHITE));
                    }
                } else {
                    foreach ($output as $item) {
                        Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColors::BLUE, ConsoleColors::WHITE));
                    }
                }
            }
        }

        $output = [];
        exec("composer remove {$package} {$version}", $output, $returnCode);
        if ($returnCode !== 0) {
            foreach ($output as $item) {
                Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColors::RED, ConsoleColors::WHITE));
            }
        } else {
            foreach ($output as $item) {
                Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColors::BLUE, ConsoleColors::WHITE));
            }
        }

    }

}
