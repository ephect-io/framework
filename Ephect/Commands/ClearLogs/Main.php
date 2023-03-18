<?php

namespace Ephect\Commands\ClearLogs;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "clear", subject: "logs")]
#[CommandDeclaration(desc: "Clear all logs.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->clearLogs();
        Console::writeLine($data);
    }
}
