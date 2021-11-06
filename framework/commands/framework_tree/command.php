<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "display-ephect-tree")]
#[CommandDeclaration(desc: "Display the tree of the Ephect framework.")]
class FrameworkTree extends AbstractAttributedCommand
{
    public function run(): void
    {
        $this->application->displayTree(EPHECT_ROOT);
    }
}
