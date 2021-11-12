<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "tree")]
#[CommandDeclaration(desc: "Display the tree of the current application.")]
class ApplicationTree extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->displayTree($this->application->appDirectory);
    }
}
