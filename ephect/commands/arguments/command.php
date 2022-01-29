<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "arguments")]
#[CommandDeclaration(desc: "Show the application arguments.")]
class Arguments extends AbstractCommand
{

    public function run(): void
    {
        $data = ['argv' => $this->application->getArgv(), 'argc' => $this->application->getArgc()];
        Console::writeLine($data);
    }
}
