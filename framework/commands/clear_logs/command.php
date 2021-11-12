<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "clear", subject: "logs")]
#[CommandDeclaration(desc: "Clear all logs.")]
class ClearLogs extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->clearLogs();
        Console::writeLine($data);
    }
}
