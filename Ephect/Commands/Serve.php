<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\CLI\System\Command;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "serve")]
#[CommandDeclaration(desc: "Lauch PHP embedded server on available port starting from the one in config.")]
class Serve extends AbstractCommand
{

    public function run(): void
    {
        $port = '8000';

        if($this->application->getArgc() > 2) {
            $customPort = $this->application->getArgv()[2];

            $cleanPort = preg_replace('/([\d]+)/', '$1', $customPort);

            if($cleanPort !== $customPort) {
                $customPort = $port;
            }
    
            $port = $customPort;
        }

        $cmd = new Command();
        $php = $cmd->which('php');

        Console::writeLine('PHP is %s', ConsoleColors::getColoredString($php, ConsoleColors::RED));
        Console::writeLine('Port is %s', ConsoleColors::getColoredString($port, ConsoleColors::RED));
        $cmd->execute($php, '-S', "localhost:$port", '-t', 'public');
        Console::writeLine("Serving the application locally ...");
    }
}
