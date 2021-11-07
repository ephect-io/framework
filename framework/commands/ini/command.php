<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;
use Ephect\Registry\Registry;

#[CommandDeclaration(verb: "show", subject: "ini")]
#[CommandDeclaration(desc: "Display the ini file if exists")]
class Ini extends AbstractCommand
{
    public function run(): void
    {
        $this->application->loadInFile();
        $data = Registry::item('ini');
        $this->application->writeLine($data);    }
}
