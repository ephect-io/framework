<?php

namespace Ephect\Samples\QuickStart\Commands\Hello;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "say", subject: "hello")]
#[CommandDeclaration(desc: "Say hello.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $data = 'world';
        $data = $this->application->getArgi(2, $data);
        Console::writeLine("Hello %s!", $data);

        return 0;
    }
}
