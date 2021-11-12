<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "name")]
#[CommandDeclaration(desc: "Display the running application name.")]
class Name extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getName();
        Console::writeLine($data);
    }
}
