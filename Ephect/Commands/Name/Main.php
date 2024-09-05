<?php

namespace Ephect\Commands\Name;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "name")]
#[CommandDeclaration(desc: "Display the running application name.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $data = $this->application->getName();
        Console::writeLine($data);

        return 0;
    }
}
