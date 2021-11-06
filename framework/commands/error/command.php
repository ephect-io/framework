<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "error")]
#[CommandDeclaration(desc: "Display the php error log.")]
class Error extends AbstractAttributedCommand
{
    public function run(): void
    {
        $data = $this->application->getPhpErrorLog();
        $this->application->writeLine($data);
    }
}
