<?php

namespace Ephect\Commands\BranchTree;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "branch-tree")]
#[CommandDeclaration(desc: "Display the tree of the Ephect framework master branch.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $dir = 'master' . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ephect';

        $egg = new EggLib($this->application);
        $egg->displayTree($dir);
    }
}
