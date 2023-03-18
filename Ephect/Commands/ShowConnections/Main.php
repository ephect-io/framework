<?php

namespace Ephect\Commands\ShowConnections;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Ephect\Framework\Registry\StateRegistry;

#[CommandDeclaration(verb: "show", subject: "connections")]
#[CommandDeclaration(desc: "Display the data connections registered.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $data = StateRegistry::item('connections');
        Console::writeLine($data);
    }
}
