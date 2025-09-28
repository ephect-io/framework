<?php

namespace Ephect\Commands\Debug;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "debug")]
#[CommandDeclaration(desc: "Display the debug log.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $data = $this->application->getDebugLog();
        Console::writeLine($data);

        return 0;
    }
}
