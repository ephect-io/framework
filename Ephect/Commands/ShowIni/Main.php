<?php

namespace Ephect\Commands\Ini;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Ephect\Framework\Registry\StateRegistry;

#[CommandDeclaration(verb: "show", subject: "ini")]
#[CommandDeclaration(desc: "Display the ini file if exists")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $this->application->loadInFile();
        $data = StateRegistry::item('ini');
        Console::writeLine($data);    }
}
