<?php

namespace Ephect\Commands\BranchTree;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Ephect\Framework\Commands\CommonLib;

#[CommandDeclaration(verb: "show", subject: "branch-tree")]
#[CommandDeclaration(desc: "Display the tree of the Ephect\Framework framework master branch.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $dir = 'master' . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ephect';

        $egg = new CommonLib($this->application);
        $egg->displayTree($dir);

        return 0;
    }
}
