<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "history")]
#[CommandDeclaration(desc: "Display the commands history.")]
class History extends AbstractCommand
{
    public function run(): void
    {
        $history = readline_list_history();
        Console::writeLine($history);
    }
}
