<?php

namespace Ephect\Commands\FrameworkTree;

use Ephect\Commands\CommonLib;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "list", subject: "framework")]
#[CommandDeclaration(desc: "Display the tree of the Ephect framework.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        Console::writeLine(EPHECT_ROOT);
        $egg = new CommonLib($this->application);
        $egg->displayTree(EPHECT_ROOT);

        return 0;
    }
}
