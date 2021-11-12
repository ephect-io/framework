<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "debug")]
#[CommandDeclaration(desc: "Display the debug log.")]
class Debug extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getDebugLog();
        Console::writeLine($data);
    }
}
