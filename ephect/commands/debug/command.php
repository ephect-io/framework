<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

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
