<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "display-tree")]
#[CommandDeclaration(desc: "Display the tree of the current application.")]
class ApplicationTree extends AbstractAttributedCommand
{
    public function run(): void
    {
        $this->application->displayTree($this->application->appDirectory);
    }
}
