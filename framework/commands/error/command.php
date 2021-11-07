<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "error")]
#[CommandDeclaration(desc: "Display the php error log.")]
class Error extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getPhpErrorLog();
        $this->application->writeLine($data);
    }
}
