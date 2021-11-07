<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "tree")]
#[CommandDeclaration(desc: "Display the tree of the current application.")]
class ApplicationTree extends AbstractCommand
{
    public function run(): void
    {
        $this->application->displayTree($this->application->appDirectory);
    }
}
