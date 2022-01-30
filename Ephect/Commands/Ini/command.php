<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Ephect\Framework\Registry\StateRegistry;

#[CommandDeclaration(verb: "show", subject: "ini")]
#[CommandDeclaration(desc: "Display the ini file if exists")]
class Ini extends AbstractCommand
{
    public function run(): void
    {
        $this->application->loadInFile();
        $data = StateRegistry::item('ini');
        Console::writeLine($data);    }
}
