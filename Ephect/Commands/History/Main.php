<?php

namespace Ephect\Commands\History;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "history")]
#[CommandDeclaration(desc: "Display the commands history.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $history = readline_list_history();
        Console::writeLine($history);

        return 0;
    }
}
