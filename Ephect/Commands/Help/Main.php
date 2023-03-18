<?php

namespace Ephect\Commands\Help;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "help")]
#[CommandDeclaration(desc: "Display this help")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $this->application->help();
    }
}
