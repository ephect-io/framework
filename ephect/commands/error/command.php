<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

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
