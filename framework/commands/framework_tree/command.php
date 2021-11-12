<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "list", subject: "framework")]
#[CommandDeclaration(desc: "Display the tree of the Ephect framework.")]
class FrameworkTree extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->displayTree(EPHECT_ROOT);
    }
}
