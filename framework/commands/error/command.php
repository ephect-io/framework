<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "error")]
#[CommandDeclaration(desc: "Display the php error log.")]
class Error extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getPhpErrorLog();
        Console::writeLine($data);
    }
}
