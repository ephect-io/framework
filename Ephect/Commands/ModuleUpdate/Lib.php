<?php

namespace Ephect\Commands\ModuleUpdate;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Modules\ModulesConfigReader;

class Lib extends AbstractCommandLib
{
    public function update(): void
    {
        $moduleConfigReader = new ModulesConfigReader();
        $modulesConfig = $moduleConfigReader->read();
        $modules = $modulesConfig->getModules();

        foreach ($modules as $package => $version) {
            exec("composer require {$package} {$version}", $output, $returnCode);
            Console::writeLine(ConsoleColors::getColoredString(
                "Installing $package $version",
                ConsoleColors::BLUE,
                ConsoleColors::WHITE
            ));

            if ($returnCode !== 0) {
                if ($returnCode !== 0) {
                    foreach ($output as $item) {
                        Console::writeLine(ConsoleColors::getColoredString(
                            $item,
                            ConsoleColors::RED,
                            ConsoleColors::WHITE
                        ));
                    }
                } else {
                    foreach ($output as $item) {
                        Console::writeLine(ConsoleColors::getColoredString(
                            $item,
                            ConsoleColors::BLUE,
                            ConsoleColors::WHITE
                        ));
                    }
                }
            }

            $binScript = \Constants::SITE_ROOT . "vendor/bin/" . str_replace('/', '_', $package) . '_install.sh';
            if (PHP_OS == 'WINNT') {
                $binScript = \Constants::SITE_ROOT . "vendor\\bin\\" . str_replace('/', '_', $package) . '_install.bat';
            }

            $output = [];
            if (file_exists($binScript)) {
                Console::writeLine(ConsoleColors::getColoredString(
                    "Running install script",
                    ConsoleColors::BLUE,
                    ConsoleColors::WHITE
                ));
                exec("$binScript", $output, $returnCode);
                if ($returnCode !== 0) {
                    foreach ($output as $item) {
                        Console::writeLine(ConsoleColors::getColoredString(
                            $item,
                            ConsoleColors::RED,
                            ConsoleColors::WHITE
                        ));
                    }
                } else {
                    foreach ($output as $item) {
                        Console::writeLine(ConsoleColors::getColoredString(
                            $item,
                            ConsoleColors::BLUE,
                            ConsoleColors::WHITE
                        ));
                    }
                }
            }
        }
    }
}
