<?php

namespace Ephect\Commands\Os;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "os")]
#[CommandDeclaration(desc: "Display the running operating system name.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getOS();
        Console::writeLine($data);
    }
}
