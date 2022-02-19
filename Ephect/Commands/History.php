<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "history")]
#[CommandDeclaration(desc: "Display the commands history.")]
class History extends AbstractCommand
{
    public function run(): void
    {
        $history = readline_list_history();
        Console::writeLine($history);
    }
}
