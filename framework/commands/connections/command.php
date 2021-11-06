<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;
use Ephect\Registry\Registry;

#[CommandDeclaration(long: "connections")]
#[CommandDeclaration(desc: "Display the data connections registered.")]
class Connections extends AbstractAttributedCommand
{
    public function run(): void
    {
        $data = Registry::item('connections');
        $this->application->writeLine($data);
    }
}
