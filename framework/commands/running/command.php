<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "phar-running")]
#[CommandDeclaration(desc: "Show Phar::running() output")]
class Running extends AbstractCommand
{
    public function run(): void
    {
        $this->application->writeLine(\Phar::running());
    }
}
