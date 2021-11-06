<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "clear-logs")]
#[CommandDeclaration(desc: "Clear all logs.")]
class ClearLogs extends AbstractAttributedCommand
{
    public function run(): void
    {
        $data = $this->application->clearLogs();
        $this->application->writeLine($data);
    }
}
