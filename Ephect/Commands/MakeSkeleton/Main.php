<?php

namespace Ephect\Commands\MakeSkeleton;

use Ephect\Commands\CommonLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "skeleton")]
#[CommandDeclaration(desc: "Create the skeleton application tree.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $egg = new CommonLib($this->application);
        $egg->createCommonTrees();
        $lib = new Lib($this->application);
        $lib->makeSkeleton();
    }
}
