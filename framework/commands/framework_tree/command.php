<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "list", subject: "framework")]
#[CommandDeclaration(desc: "Display the tree of the Ephect framework.")]
class FrameworkTree extends AbstractCommand
{
    public function run(): void
    {
        $this->application->displayTree(EPHECT_ROOT);
    }
}
