<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

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
