<?php

namespace Ephect\Commands\ShowArguments;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "arguments")]
#[CommandDeclaration(desc: "Show the application arguments.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $data = ['argv' => $this->application->getArgv(), 'argc' => $this->application->getArgc()];
        Console::writeLine($data);

        return 0;
    }
}
