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
        // $data = ['argv' => $this->application->getArgv(), 'argc' => $this->application->getArgc()];
        $cmd = new Command();
        $php = $cmd->which('php');

        Console::writeLine('PHP is %s', ConsoleColors::getColoredString($php, ConsoleColors::RED));
        $cmd->execute($php, '-S', 'localhost:8000', '-t', 'public');
        Console::writeLine("Serving the application locally ...");
    }
}
