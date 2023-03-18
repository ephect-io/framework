<?php

namespace Ephect\Commands\Running;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Phar;

#[CommandDeclaration(verb: "show", subject: "phar-running")]
#[CommandDeclaration(desc: "Show Phar::running() output")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        Console::writeLine(Phar::running());
    }
}
