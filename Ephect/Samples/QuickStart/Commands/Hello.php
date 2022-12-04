<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "say", subject: "hello")]
#[CommandDeclaration(desc: "Say hello.")]
class Hello extends AbstractCommand
{
    public function run(): void
    {
        $data = 'world';
        if($this->application->getArgc() > 2) {
            $data = $this->application->getArgv()[2];
        }
        Console::writeLine("Hello %s!", $data);
    }
}
