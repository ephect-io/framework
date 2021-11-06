<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "help", short: "h")]
#[CommandDeclaration(desc: "Display this help")]
class Help extends AbstractCommand
{
    public function run(): void
    {
        $this->application->help();
    }
}
