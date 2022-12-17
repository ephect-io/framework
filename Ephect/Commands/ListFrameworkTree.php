<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "list", subject: "framework")]
#[CommandDeclaration(desc: "Display the tree of the Ephect framework.")]
class FrameworkTree extends AbstractCommand
{
    public function run(): void
    {
        Console::writeLine(EPHECT_ROOT);
        $egg = new EggLib($this->application);
        $egg->displayTree(EPHECT_ROOT);
    }
}
