<?php

namespace Ephect\Modules\Samples\Commands\MakeSkeleton;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Ephect\Modules\Samples\Commands\MakeSkeleton\Lib;
use Ephect\Samples\Common;

#[CommandDeclaration(verb: "make", subject: "skeleton")]
#[CommandDeclaration(desc: "Create the skeleton application tree.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $egg = new Common;
        $egg->createCommonTrees();
        $lib = new Lib($this->application);
        $lib->makeSkeleton();

        return 0;
    }
}
