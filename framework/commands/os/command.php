<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "os")]
#[CommandDeclaration(desc: "Display the running operating system name.")]
class Os extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getOS();
        Console::writeLine($data);
    }
}
