<?php

namespace Ephect\Commands\ClearAll;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "clear", subject: "all")]
#[CommandDeclaration(desc: "Clear cache, runtime and log files.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $data = $this->application->clearLogs();
        Console::writeLine($data);
        $data = $this->application->clearRuntime();
        Console::writeLine($data);

        return 0;
    }
}
