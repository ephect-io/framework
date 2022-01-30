<?php

namespace Ephect\Commands;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Phar;

#[CommandDeclaration(verb: "show", subject: "phar-running")]
#[CommandDeclaration(desc: "Show Phar::running() output")]
class Running extends AbstractCommand
{
    public function run(): void
    {
        Console::writeLine(Phar::running());
    }
}
