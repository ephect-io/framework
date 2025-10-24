<?php

namespace Ephect\Modules\Samples\Commands\MakeSkeleton;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Ephect\Modules\Samples\Commands\Common;
use Ephect\Modules\Samples\Commands\MakeSkeleton\Lib;

#[CommandDeclaration(verb: "make", subject: "skeleton")]
#[CommandDeclaration(desc: "Create the skeleton application tree.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $use = new Common();
        $use->createCommonTrees();
        $lib = new Lib($this->application);
        $lib->makeSkeleton();

        return 0;
    }
}
