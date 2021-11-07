<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "debug")]
#[CommandDeclaration(desc: "Display the debug log.")]
class Debug extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getDebugLog();
        $this->application->writeLine($data);
    }
}
