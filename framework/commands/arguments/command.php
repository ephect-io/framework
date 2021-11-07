<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "arguments")]
#[CommandDeclaration(desc: "Show the application arguments.")]
class Arguments extends AbstractCommand
{

    public function run(): void
    {
        $data = ['argv' => $this->application->getArgv(), 'argc' => $this->application->getArgc()];
        $this->application->writeLine($data);
    }
}
