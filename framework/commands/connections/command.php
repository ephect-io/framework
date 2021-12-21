<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;
use Ephect\Registry\StateRegistry;

#[CommandDeclaration(verb: "show", subject: "connections")]
#[CommandDeclaration(desc: "Display the data connections registered.")]
class Connections extends AbstractCommand
{
    public function run(): void
    {
        $data = StateRegistry::item('connections');
        Console::writeLine($data);
    }
}
