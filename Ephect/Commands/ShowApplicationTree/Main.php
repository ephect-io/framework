<?php

namespace Ephect\Commands\ApplicationTree;

use Ephect\Commands\CommonLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "tree")]
#[CommandDeclaration(desc: "Display the tree of the current application.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $egg = new CommonLib($this->application);
        $egg->displayTree(APP_DIR);
    }
}
