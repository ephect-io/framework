<?php

namespace Ephect\Commands\Title;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "title")]
#[CommandDeclaration(desc: "Display the running application title.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getTitle();
        Console::writeLine($data);
    }
}
