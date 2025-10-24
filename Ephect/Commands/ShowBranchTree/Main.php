<?php

namespace Ephect\Commands\ShowBranchTree;

use Ephect\Commands\CommonLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "branch-tree")]
#[CommandDeclaration(desc: "Display the tree of the Ephect\Framework framework master branch.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $dir = 'master' . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ephect';

        $use = new CommonLib($this->application);
        $use->displayTree($dir);

        return 0;
    }
}
