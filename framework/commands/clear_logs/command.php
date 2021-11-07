<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "clear", subject: "logs")]
#[CommandDeclaration(desc: "Clear all logs.")]
class ClearLogs extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->clearLogs();
        $this->application->writeLine($data);
    }
}
