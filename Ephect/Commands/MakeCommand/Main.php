<?php

namespace Ephect\Commands\MakeCommand;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "command")]
#[CommandDeclaration(desc: "Create the base tree of a command.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $lib = new Lib($this->application);
        $lib->createCommandBase();
        $this->application->clearRuntime();
    }
}
