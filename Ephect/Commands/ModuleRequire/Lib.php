<?php

namespace Ephect\Commands\ModuleRequire;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;

class Lib extends AbstractCommandLib
{
    public function require(string $package, string $version): void
    {
        exec("composer require {$package} {$version}", $output, $returnCode);
        if ($returnCode !== 0) {
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

        $binScript = SITE_ROOT . "vendor/bin/" . str_replace('/', '_', $package) . '_install.sh';
        if(PHP_OS == 'WINNT') {
            $binScript = SITE_ROOT . "vendor\\bin\\" . str_replace('/', '_', $package) . '_install.bat';
        }

        $output = [];
        if(file_exists($binScript)) {
            Console::writeLine(ConsoleColors::getColoredString("An install script was found", ConsoleColors::BLUE, ConsoleColors::WHITE));
            if(Console::readYesOrNo("Do you want to run the script?")) {
                exec("$binScript", $output, $returnCode);
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

    }

}

