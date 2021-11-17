<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "clear", subject: "cache")]
#[CommandDeclaration(desc: "Clear all cache files.")]
class ClearCache extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->clearCache();
        Console::writeLine($data);
    }
}
