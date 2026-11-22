<?php

namespace Ephect\Commands\ModuleRequire;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\CLI\Enums\ConsoleColorsEnum;
use Ephect\Framework\Commands\AbstractCommandLib;

class Lib extends AbstractCommandLib
{
    public function require(string $package, string $version): void
    {
        exec("composer require {$package} {$version}", $output, $returnCode);
        if ($returnCode !== 0) {
            if ($returnCode !== 0) {
                foreach ($output as $item) {
                    Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColorsEnum::RED, ConsoleColorsEnum::WHITE));
                }
            } else {
                foreach ($output as $item) {
                    Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColorsEnum::BLUE, ConsoleColorsEnum::WHITE));
                }
            }
        }

        $binScript = \Constants::SITE_ROOT . "vendor/bin/" . str_replace('/', '_', $package) . '_install.sh';
        if (PHP_OS == 'WINNT') {
            $binScript = \Constants::SITE_ROOT . "vendor\\bin\\" . str_replace('/', '_', $package) . '_install.bat';
        }

        $output = [];
        if (file_exists($binScript)) {
            Console::writeLine(ConsoleColors::getColoredString("An install script was found", ConsoleColorsEnum::BLUE, ConsoleColorsEnum::WHITE));
            if (Console::readYesOrNo("Do you want to run the script?")) {
                exec("$binScript", $output, $returnCode);
                if ($returnCode !== 0) {
                    foreach ($output as $item) {
                        Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColorsEnum::RED, ConsoleColorsEnum::WHITE));
                    }
                } else {
                    foreach ($output as $item) {
                        Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColorsEnum::BLUE, ConsoleColorsEnum::WHITE));
                    }
                }
            }
        }
    }
}
