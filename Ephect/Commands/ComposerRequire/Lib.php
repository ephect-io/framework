<?php

namespace Ephect\Commands\ComposerRequire;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;

class Lib extends AbstractCommandLib
{
    public function require(string $package, string $version): void
    {
        exec("composer require {$package} {$version}", $output, $returnCode);
        if ($returnCode !== 0) {
            foreach ($output as $item) {
                Console::writeLine(ConsoleColors::getColoredString($item, ConsoleColors::RED, ConsoleColors::WHITE));
            }
        }

        $binScript = SITE_ROOT . "/vendor/bin/" . str_replace('/', '_', $package) . '_install.sh';

        Console::writeLine("Bin script: %s", $binScript);
        if(file_exists($binScript)) {
            Console::writeLine("Bin script exists");
            exec("$binScript", $output, $returnCode);
        }
    }

}

