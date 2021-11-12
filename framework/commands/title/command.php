<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "title")]
#[CommandDeclaration(desc: "Display the running application title.")]
class Title extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getTitle();
        Console::writeLine($data);
    }
}
