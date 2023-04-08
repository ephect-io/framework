<?php

namespace Ephect\Commands\ClearCache;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "clear", subject: "cache")]
#[CommandDeclaration(desc: "Clear all cache files.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->clearRuntime();
        Console::writeLine($data);
    }
}
