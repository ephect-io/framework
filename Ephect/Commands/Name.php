<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

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
