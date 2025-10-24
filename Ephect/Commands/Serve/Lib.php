<?php

namespace Ephect\Commands\Serve;

use Ephect\Commands\CommonLib;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\CLI\System\Command;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Utils\File;

class Lib extends AbstractCommandLib
{
    public function Serve(): void
    {
        $use = new CommonLib($this->parent);
        $port = $this->getPort();

        File::safeWrite(\Constants::CONFIG_DIR . 'dev_port', $port);

        $cmd = new Command();
        $php = $cmd->which('php');

        Console::writeLine('PHP is %s', ConsoleColors::getColoredString($php, ConsoleColors::RED));
        Console::writeLine('Port is %s', ConsoleColors::getColoredString($port, ConsoleColors::RED));
        $cmd->execute($php, '-S', "localhost:$port", '-t', \Constants::CONFIG_DOCROOT);
        Console::writeLine("Serving the application locally ...");
    }

    public function getPort($default = 8000): int
    {
        $port = $default;

        if ($this->parent->getArgc() > 2) {
            $customPort = $this->parent->getArgi(2);

            $cleanPort = preg_replace('/([\d]+)/', '$1', $customPort);

            if ($cleanPort !== $customPort) {
                $customPort = $port;
            }

            $port = $customPort;
        }

        return $port;
    }
}
