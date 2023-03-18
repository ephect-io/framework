<?php

namespace Ephect\Commands\Error;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "error")]
#[CommandDeclaration(desc: "Display the php error log.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getPhpErrorLog();
        Console::writeLine($data);
    }
}
